<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

use Doctrine\DBAL\Connection;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class ProjectController extends AbstractController
{
    #[Route('/project/{id}', name: 'app_project_show')]
    #[IsGranted('ROLE_USER')]
    public function show(int $id, Connection $connection, CacheInterface $cache): Response
    {
        $project = $connection->fetchAssociative('SELECT * FROM project WHERE project_id = ?', [$id]);

        if (!$project) {
            throw $this->createNotFoundException('Project not found');
        }

        // Fetch stats with caching
        $stats = $cache->get("project_{$id}_stats", function (ItemInterface $item) use ($connection, $id) {
            $item->expiresAfter(60);
            return [
                'requirements' => $connection->fetchOne('SELECT COUNT(*) FROM requirement WHERE project_id = ?', [$id]),
                'bugs' => $connection->fetchOne('SELECT COUNT(*) FROM bug WHERE projectid = ?', [$id]),
                'tests' => $connection->fetchOne('SELECT COUNT(*) FROM testsuite WHERE project_id = ?', [$id]),
            ];
        });

        // Grouped stats for charts with caching
        $bugSeverity = $cache->get("project_{$id}_bug_severity", function (ItemInterface $item) use ($connection, $id) {
            $item->expiresAfter(60);
            return $connection->fetchAllAssociative('
                SELECT severity, COUNT(*) as count 
                FROM bug 
                WHERE projectid = ? 
                GROUP BY severity
            ', [$id]);
        });

        $testStatus = $cache->get("project_{$id}_test_status", function (ItemInterface $item) use ($connection, $id) {
            $item->expiresAfter(60);
            return $connection->fetchAllAssociative('
                SELECT status, COUNT(*) as count 
                FROM testsuite 
                WHERE project_id = ? 
                GROUP BY status
            ', [$id]);
        });

        $reqStatus = $cache->get("project_{$id}_req_status", function (ItemInterface $item) use ($connection, $id) {
            $item->expiresAfter(60);
            return $connection->fetchAllAssociative('
                SELECT rv.status, COUNT(*) as count 
                FROM requirementversion rv 
                JOIN requirement r ON rv.reqid = r.reqid 
                WHERE r.project_id = ? AND rv.latest = \'Y\' 
                GROUP BY rv.status
            ', [$id]);
        });

        return $this->render('project/show.html.twig', [
            'project' => $project,
            'stats' => $stats,
            'bugSeverity' => $bugSeverity,
            'testStatus' => $testStatus,
            'reqStatus' => $reqStatus,
        ]);
    }

    #[Route('/project/{id}/requirements', name: 'app_project_requirements')]
    #[IsGranted('ROLE_USER')]
    public function requirements(int $id, Request $request, Connection $connection): Response
    {
        $project = $connection->fetchAssociative('SELECT * FROM project WHERE project_id = ?', [$id]);

        if (!$project) {
            throw $this->createNotFoundException('Project not found');
        }

        $search = $request->query->get('q');
        $params = [$id];
        $sql = '
            SELECT r.*, rv.version, rv.status, rv.author
            FROM requirement r
            LEFT JOIN requirementversion rv ON r.reqid = rv.reqid AND rv.latest = \'Y\'
            WHERE r.project_id = ?
        ';

        if ($search) {
            $sql .= ' AND (r.reqname ILIKE ? OR rv.detail ILIKE ?)';
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
        }

        $sql .= ' ORDER BY r.reqid ASC';

        $requirements = $connection->fetchAllAssociative($sql, $params);

        $response = $this->render('project/requirements.html.twig', [
            'project' => $project,
            'requirements' => $requirements,
            'search' => $search,
        ]);

        $response->setMaxAge(60);
        $response->setSharedMaxAge(60);

        return $response;
    }

    #[Route('/project/{id}/tests', name: 'app_project_tests')]
    #[IsGranted('ROLE_USER')]
    public function tests(int $id, Connection $connection): Response
    {
        $project = $connection->fetchAssociative('SELECT * FROM project WHERE project_id = ?', [$id]);

        if (!$project) {
            throw $this->createNotFoundException('Project not found');
        }

        $tests = $connection->fetchAllAssociative('
            SELECT *
            FROM testsuite
            WHERE project_id = ?
            ORDER BY testid ASC
        ', [$id]);

        return $this->render('project/tests.html.twig', [
            'project' => $project,
            'tests' => $tests,
        ]);
    }

    #[Route('/project/{id}/tests/new', name: 'app_project_test_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function testNew(int $id, Request $request, Connection $connection): Response
    {
        $project = $connection->fetchAssociative('SELECT * FROM project WHERE project_id = ?', [$id]);

        if (!$project) {
            throw $this->createNotFoundException('Project not found');
        }

        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $purpose = $request->request->get('description');
            $priority = $request->request->get('priority');
            $type = $request->request->get('type');
            $status = $request->request->get('status');

            $connection->insert('testsuite', [
                'project_id' => $id,
                'testsuitename' => $name,
                'purpose' => $purpose,
                'priority' => $priority,
                'testtype' => $type,
                'status' => $status,
                'datecreated' => date('Y-m-d H:i:s'),
                'lastupdated' => date('Y-m-d H:i:s'),
                'lastupdatedby' => $this->getUser()->getUserIdentifier(),
                'deleted' => 'N',
                'archive' => 'N',
                'codereview' => 'N',
                'ba_approval' => 'N',
                'steps' => 'N',
                'script' => 'N',
                'loadrunner' => 'N',
                'autopass' => 'N',
                'email_ba_owner' => 'N',
                'email_qa_owner' => 'N',
                'duration' => '',
                'areatested' => '',
                'baowner' => '',
                'scripter' => '',
                'approvedforauto' => 'N',
                'comments' => '',
                'assignedto' => '',
                'assignedby' => '',
                'dateassigned' => '',
                'expdatecomplete' => '',
                'actdatecomplete' => '',
                'basignoff' => '',
                'uniqueid' => uniqid(),
            ]);

            return $this->redirectToRoute('app_project_tests', ['id' => $id]);
        }

        return $this->render('project/test_new.html.twig', [
            'project' => $project,
        ]);
    }

    #[Route('/project/{id}/tests/{testId}/edit', name: 'app_project_test_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function testEdit(int $id, int $testId, Request $request, Connection $connection): Response
    {
        $project = $connection->fetchAssociative('SELECT * FROM project WHERE project_id = ?', [$id]);
        $test = $connection->fetchAssociative('SELECT * FROM testsuite WHERE testid = ? AND project_id = ?', [$testId, $id]);

        if (!$project || !$test) {
            throw $this->createNotFoundException('Test case not found');
        }

        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $purpose = $request->request->get('description');
            $priority = $request->request->get('priority');
            $status = $request->request->get('status');

            $connection->update('testsuite', [
                'testsuitename' => $name,
                'purpose' => $purpose,
                'priority' => $priority,
                'status' => $status,
                'lastupdated' => date('Y-m-d H:i:s'),
                'lastupdatedby' => $this->getUser()->getUserIdentifier(),
            ], ['testid' => $testId]);

            return $this->redirectToRoute('app_project_tests', ['id' => $id]);
        }

        return $this->render('project/test_edit.html.twig', [
            'project' => $project,
            'test' => $test,
        ]);
    }

    #[Route('/project/{id}/tests/{testId}/steps', name: 'app_project_test_steps', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function testSteps(int $id, int $testId, Request $request, Connection $connection): Response
    {
        $project = $connection->fetchAssociative('SELECT * FROM project WHERE project_id = ?', [$id]);
        $test = $connection->fetchAssociative('SELECT * FROM testsuite WHERE testid = ? AND project_id = ?', [$testId, $id]);

        if (!$project || !$test) {
            throw $this->createNotFoundException('Test case not found');
        }

        if ($request->isMethod('POST')) {
            $action = $request->request->get('action');
            if ($action === 'add') {
                $connection->insert('teststep', [
                    'testid' => $testId,
                    'teststep_number' => $request->request->get('step_number'),
                    'action' => $request->request->get('step_action'),
                    'inputs' => $request->request->get('inputs'),
                    'expected_result' => $request->request->get('expected_result'),
                    'steptype' => 'Manual',
                ]);
            } elseif ($action === 'update') {
                $stepId = $request->request->get('step_id');
                $connection->update('teststep', [
                    'teststep_number' => $request->request->get('step_number'),
                    'action' => $request->request->get('step_action'),
                    'inputs' => $request->request->get('inputs'),
                    'expected_result' => $request->request->get('expected_result'),
                ], ['teststepid' => $stepId]);
            }
            return $this->redirectToRoute('app_project_test_steps', ['id' => $id, 'testId' => $testId]);
        }

        $steps = $connection->fetchAllAssociative('
            SELECT * FROM teststep 
            WHERE testid = ? 
            ORDER BY teststep_number ASC
        ', [$testId]);

        return $this->render('project/test_steps.html.twig', [
            'project' => $project,
            'test' => $test,
            'steps' => $steps,
        ]);
    }

    #[Route('/project/{id}/tests/{testId}/steps/{stepId}/delete', name: 'app_project_test_step_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function testStepDelete(int $id, int $testId, int $stepId, Connection $connection): Response
    {
        $connection->delete('teststep', ['teststepid' => $stepId, 'testid' => $testId]);
        return $this->redirectToRoute('app_project_test_steps', ['id' => $id, 'testId' => $testId]);
    }

    #[Route('/project/{id}/tests/{testId}/delete', name: 'app_project_test_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function testDelete(int $id, int $testId, Connection $connection): Response
    {
        $connection->delete('testsuite', ['testid' => $testId, 'project_id' => $id]);

        return $this->redirectToRoute('app_project_tests', ['id' => $id]);
    }

    #[Route('/project/{id}/tests/{testId}/retire', name: 'app_project_test_retire', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function testRetire(int $id, int $testId, Connection $connection): Response
    {
        $connection->update('testsuite', [
            'status' => 'Retired',
        ], ['testid' => $testId, 'project_id' => $id]);

        return $this->redirectToRoute('app_project_tests', ['id' => $id]);
    }

    #[Route('/project/{id}/bugs', name: 'app_project_bugs')]
    #[IsGranted('ROLE_USER')]
    public function bugs(int $id, Request $request, Connection $connection): Response
    {
        $project = $connection->fetchAssociative('SELECT * FROM project WHERE project_id = ?', [$id]);

        if (!$project) {
            throw $this->createNotFoundException('Project not found');
        }

        $search = $request->query->get('q');
        $params = [$id];
        $sql = 'SELECT * FROM bug WHERE projectid = ?';

        if ($search) {
            $sql .= ' AND (summary ILIKE ? OR description ILIKE ?)';
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
        }

        $sql .= ' ORDER BY bugid DESC';

        $bugs = $connection->fetchAllAssociative($sql, $params);

        $response = $this->render('project/bugs.html.twig', [
            'project' => $project,
            'bugs' => $bugs,
            'search' => $search,
        ]);

        $response->setMaxAge(60);
        $response->setSharedMaxAge(60);

        return $response;
    }

    #[Route('/project/{id}/test-runs/new', name: 'app_project_test_run_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function testRunNew(int $id, Request $request, Connection $connection): Response
    {
        $project = $connection->fetchAssociative('SELECT * FROM project WHERE project_id = ?', [$id]);

        if (!$project) {
            throw $this->createNotFoundException('Project not found');
        }

        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $buildId = $request->request->get('build_id');
            $testIds = $request->request->all('test_ids');

            $connection->insert('testset', [
                'buildid' => (int)$buildId,
                'testsetname' => $name,
                'datecreated' => date('Y-m-d H:i:s'),
                'testsetstatus' => 'In Progress',
                'uniqueid' => substr(uniqid(), 0, 15),
                'archive' => 'N',
                'testsetorderby' => 0,
                'signoffdate' => '',
                'signoffby' => '',
                'signoffcomments' => '',
                'locked' => 'N',
            ]);

            // For PostgreSQL/Doctrine, we can fetch the sequence value
            $runId = $connection->lastInsertId('testset_testsetid_seq');

            foreach ($testIds as $testId) {
                $connection->insert('testset_testsuite_assoc', [
                    'testsetid' => $runId,
                    'testid' => (int)$testId,
                    'teststatus' => 'Not Started',
                    'assignedto' => '',
                    'comments' => '',
                    'finished' => 0,
                    'logtimestamp' => date('Y-m-d H:i:s'),
                ]);
            }

            return $this->redirectToRoute('app_project_test_runs', ['id' => $id]);
        }

        $builds = $connection->fetchAllAssociative('
            SELECT b.buildid, b.buildname, r.releasename 
            FROM build b 
            JOIN release_tbl r ON b.releaseid = r.releaseid 
            WHERE r.project_id = ?
            ORDER BY b.buildid DESC
        ', [$id]);

        $testCases = $connection->fetchAllAssociative('
            SELECT testid, testsuitename 
            FROM testsuite 
            WHERE project_id = ? AND (status != \'Retired\' OR status IS NULL)
            ORDER BY testsuitename ASC
        ', [$id]);

        return $this->render('project/test_run_new.html.twig', [
            'project' => $project,
            'builds' => $builds,
            'testCases' => $testCases,
        ]);
    }

    #[Route('/project/{id}/test-runs', name: 'app_project_test_runs')]
    #[IsGranted('ROLE_USER')]
    public function testRuns(int $id, Connection $connection): Response
    {
        $project = $connection->fetchAssociative('SELECT * FROM project WHERE project_id = ?', [$id]);

        if (!$project) {
            throw $this->createNotFoundException('Project not found');
        }

        $testRuns = $connection->fetchAllAssociative('
            SELECT ts.*, b.buildname, r.releasename
            FROM testset ts
            JOIN build b ON ts.buildid = b.buildid
            JOIN release_tbl r ON b.releaseid = r.releaseid
            WHERE r.project_id = ?
            ORDER BY ts.testsetid DESC
        ', [$id]);

        $response = $this->render('project/test_runs.html.twig', [
            'project' => $project,
            'testRuns' => $testRuns,
        ]);

        $response->setMaxAge(60);
        $response->setSharedMaxAge(60);

        return $response;
    }

    #[Route('/project/{id}/test-runs/{runId}', name: 'app_project_test_run_execute')]
    #[IsGranted('ROLE_USER')]
    public function executeTestRun(int $id, int $runId, Connection $connection): Response
    {
        $project = $connection->fetchAssociative('SELECT * FROM project WHERE project_id = ?', [$id]);
        $testRun = $connection->fetchAssociative('SELECT * FROM testset WHERE testsetid = ?', [$runId]);

        if (!$project || !$testRun) {
            throw $this->createNotFoundException('Project or Test Run not found');
        }

        $tests = $connection->fetchAllAssociative('
            SELECT t.*, assoc.teststatus, assoc.assignedto, assoc.testset_testsuite_associd
            FROM testsuite t
            JOIN testset_testsuite_assoc assoc ON t.testid = assoc.testid
            WHERE assoc.testsetid = ?
            ORDER BY t.testid ASC
        ', [$runId]);

        return $this->render('project/test_run_execute.html.twig', [
            'project' => $project,
            'testRun' => $testRun,
            'tests' => $tests,
        ]);
    }

    #[Route('/project/{id}/requirements/new', name: 'app_project_requirement_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function requirementNew(int $id, Request $request, Connection $connection): Response
    {
        $project = $connection->fetchAssociative('SELECT * FROM project WHERE project_id = ?', [$id]);

        if (!$project) {
            throw $this->createNotFoundException('Project not found');
        }

        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $detail = $request->request->get('detail');
            $priority = $request->request->get('priority');
            $type = $request->request->get('type');

            $connection->insert('requirement', [
                'project_id' => $id,
                'reqname' => $name,
                'priority' => $priority,
                'type' => (int)$type,
                'recordorfile' => 'R',
                'datecreated' => date('Y-m-d H:i:s'),
                'logtimestamp' => date('Y-m-d H:i:s'),
                'lastupdated' => date('Y-m-d H:i:s'),
            ]);

            $reqId = $connection->lastInsertId();

            $connection->insert('requirementversion', [
                'reqid' => $reqId,
                'version' => '1.0',
                'latest' => 'Y',
                'status' => 'New',
                'author' => $this->getUser()->getUserIdentifier(),
                'detail' => $detail,
                'timestamp' => date('Y-m-d H:i:s'),
                'lastupdated' => date('Y-m-d H:i:s'),
                'lastupdatedby' => $this->getUser()->getUserIdentifier(),
            ]);

            return $this->redirectToRoute('app_project_requirements', ['id' => $id]);
        }

        return $this->render('project/requirement_new.html.twig', [
            'project' => $project,
        ]);
    }

    #[Route('/project/{id}/requirements/{reqId}/edit', name: 'app_project_requirement_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function requirementEdit(int $id, int $reqId, Request $request, Connection $connection): Response
    {
        $project = $connection->fetchAssociative('SELECT * FROM project WHERE project_id = ?', [$id]);
        $requirement = $connection->fetchAssociative('
            SELECT r.*, rv.detail, rv.status, rv.version 
            FROM requirement r 
            LEFT JOIN requirementversion rv ON r.reqid = rv.reqid AND rv.latest = \'Y\' 
            WHERE r.reqid = ? AND r.project_id = ?
        ', [$reqId, $id]);

        if (!$project || !$requirement) {
            throw $this->createNotFoundException('Requirement not found');
        }

        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $detail = $request->request->get('detail');
            $priority = $request->request->get('priority');
            $type = $request->request->get('type');
            $status = $request->request->get('status');

            $connection->update('requirement', [
                'reqname' => $name,
                'priority' => $priority,
                'type' => (int)$type,
                'lastupdated' => date('Y-m-d H:i:s'),
            ], ['reqid' => $reqId]);

            $connection->update('requirementversion', [
                'detail' => $detail,
                'status' => $status,
                'lastupdated' => date('Y-m-d H:i:s'),
                'lastupdatedby' => $this->getUser()->getUserIdentifier(),
            ], ['reqid' => $reqId, 'latest' => 'Y']);

            return $this->redirectToRoute('app_project_requirements', ['id' => $id]);
        }

        return $this->render('project/requirement_edit.html.twig', [
            'project' => $project,
            'requirement' => $requirement,
        ]);
    }

    #[Route('/project/{id}/requirements/{reqId}/delete', name: 'app_project_requirement_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function requirementDelete(int $id, int $reqId, Connection $connection): Response
    {
        // Delete versions first (foreign key)
        $connection->delete('requirementversion', ['reqid' => $reqId]);
        $connection->delete('requirement', ['reqid' => $reqId, 'project_id' => $id]);

        return $this->redirectToRoute('app_project_requirements', ['id' => $id]);
    }

    #[Route('/project/{id}/bugs/new', name: 'app_project_bug_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function bugNew(int $id, Request $request, Connection $connection): Response
    {
        $project = $connection->fetchAssociative('SELECT * FROM project WHERE project_id = ?', [$id]);

        if (!$project) {
            throw $this->createNotFoundException('Project not found');
        }

        if ($request->isMethod('POST')) {
            $summary = $request->request->get('summary');
            $description = $request->request->get('description');
            $priority = $request->request->get('priority');
            $severity = $request->request->get('severity');

            $connection->insert('bug', [
                'projectid' => $id,
                'summary' => $summary,
                'description' => $description,
                'priority' => $priority,
                'severity' => $severity,
                'status' => 'New',
                'reporter' => $this->getUser()->getUserIdentifier(),
                'reporteddate' => date('Y-m-d H:i:s'),
                'closed' => 'N',
            ]);

            return $this->redirectToRoute('app_project_bugs', ['id' => $id]);
        }

        return $this->render('project/bug_new.html.twig', [
            'project' => $project,
        ]);
    }

    #[Route('/project/{id}/bugs/{bugId}/edit', name: 'app_project_bug_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function bugEdit(int $id, int $bugId, Request $request, Connection $connection): Response
    {
        $project = $connection->fetchAssociative('SELECT * FROM project WHERE project_id = ?', [$id]);
        $bug = $connection->fetchAssociative('SELECT * FROM bug WHERE bugid = ? AND projectid = ?', [$bugId, $id]);

        if (!$project || !$bug) {
            throw $this->createNotFoundException('Bug not found');
        }

        if ($request->isMethod('POST')) {
            $summary = $request->request->get('summary');
            $description = $request->request->get('description');
            $priority = $request->request->get('priority');
            $severity = $request->request->get('severity');
            $status = $request->request->get('status');

            $connection->update('bug', [
                'summary' => $summary,
                'description' => $description,
                'priority' => $priority,
                'severity' => $severity,
                'status' => $status,
                'closed' => ($status === 'Closed' || $status === 'Resolved') ? 'Y' : 'N',
            ], ['bugid' => $bugId]);

            return $this->redirectToRoute('app_project_bugs', ['id' => $id]);
        }

        return $this->render('project/bug_edit.html.twig', [
            'project' => $project,
            'bug' => $bug,
        ]);
    }

    #[Route('/project/{id}/bugs/{bugId}/delete', name: 'app_project_bug_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function bugDelete(int $id, int $bugId, Connection $connection): Response
    {
        $connection->delete('bug', ['bugid' => $bugId, 'projectid' => $id]);

        return $this->redirectToRoute('app_project_bugs', ['id' => $id]);
    }

    #[Route('/project/{id}/bugs/{bugId}/resolve', name: 'app_project_bug_resolve', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function bugResolve(int $id, int $bugId, Connection $connection): Response
    {
        $connection->update('bug', [
            'status' => 'Resolved',
            'closed' => 'Y',
        ], ['bugid' => $bugId, 'projectid' => $id]);

        return $this->redirectToRoute('app_project_bugs', ['id' => $id]);
    }

    #[Route('/project/{id}/report', name: 'app_project_report')]
    #[IsGranted('ROLE_USER')]
    public function projectReport(int $id, Connection $connection): Response
    {
        $project = $connection->fetchAssociative('SELECT * FROM project WHERE project_id = ?', [$id]);

        if (!$project) {
            throw $this->createNotFoundException('Project not found');
        }

        // Fetch comprehensive report data
        $requirements = $connection->fetchAllAssociative('
            SELECT r.*, rv.version, rv.status 
            FROM requirement r 
            LEFT JOIN requirementversion rv ON r.reqid = rv.reqid AND rv.latest = \'Y\' 
            WHERE r.project_id = ?
        ', [$id]);

        $bugs = $connection->fetchAllAssociative('SELECT * FROM bug WHERE projectid = ? ORDER BY severity DESC', [$id]);
        
        $stats = [
            'total_reqs' => count($requirements),
            'total_bugs' => count($bugs),
            'open_bugs' => $connection->fetchOne('SELECT COUNT(*) FROM bug WHERE projectid = ? AND closed = \'N\'', [$id]),
            'critical_bugs' => $connection->fetchOne('SELECT COUNT(*) FROM bug WHERE projectid = ? AND severity IN (\'Critical\', \'Blocker\')', [$id]),
        ];

        $response = $this->render('project/report.html.twig', [
            'project' => $project,
            'requirements' => $requirements,
            'bugs' => $bugs,
            'stats' => $stats,
        ]);

        $response->setMaxAge(300); // Reports can be cached longer
        $response->setSharedMaxAge(300);

        return $response;
    }
}
