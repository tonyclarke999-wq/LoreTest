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
            SELECT r.*, rv.version, rv.status, rv.author, rv.assignedto, rv.defect_id,
                   rdt.reqdoctypename AS doc_type_name,
                   rac.areacoverage AS area_name
            FROM requirement r
            LEFT JOIN requirementversion rv ON r.reqid = rv.reqid AND rv.latest = \'Y\'
            LEFT JOIN requirementdocumenttype rdt ON r.type = rdt.reqdoctypeid
            LEFT JOIN requirementareacoverage rac ON r.areacovered = rac.reqareacoverageid
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
            $name = trim($request->request->get('name', ''));
            $purpose = trim($request->request->get('purpose', ''));
            $comments = trim($request->request->get('comments', ''));
            $priority = $request->request->get('priority', 'Medium');
            $status = $request->request->get('status', 'New');
            $areaTested = $request->request->get('areatested', '');
            $testType = $request->request->get('testtype', 'Functional');
            $baOwner = $request->request->get('baowner', '');
            $qaOwner = $request->request->get('scripter', '');
            $tester = $request->request->get('tester', '');
            $assignedTo = $request->request->get('assignedto', '');
            $assignedBy = $request->request->get('assignedby', '');
            $dateAssigned = $request->request->get('dateassigned', '');
            $dateExpected = $request->request->get('expdatecomplete', '');
            $dateComplete = $request->request->get('actdatecomplete', '');
            $duration = trim($request->request->get('duration', ''));
            $autopass = $request->request->get('autopass') ? 'Y' : 'N';
            $emailBa = $request->request->get('email_ba_owner') ? 'Y' : 'N';
            $emailQa = $request->request->get('email_qa_owner') ? 'Y' : 'N';
            $manual = $request->request->get('steps') ? 'Y' : 'N';
            $automated = $request->request->get('script') ? 'Y' : 'N';
            $loadrunner = $request->request->get('loadrunner') ? 'Y' : 'N';

            $connection->insert('testsuite', [
                'project_id' => $id,
                'testsuitename' => $name,
                'purpose' => $purpose,
                'comments' => $comments,
                'priority' => $priority,
                'status' => $status,
                'areatested' => $areaTested,
                'testtype' => $testType,
                'baowner' => $baOwner,
                'scripter' => $qaOwner,
                'tester' => $tester,
                'assignedto' => $assignedTo,
                'assignedby' => $assignedBy,
                'dateassigned' => $dateAssigned,
                'expdatecomplete' => $dateExpected,
                'actdatecomplete' => $dateComplete,
                'duration' => $duration,
                'autopass' => $autopass,
                'email_ba_owner' => $emailBa,
                'email_qa_owner' => $emailQa,
                'steps' => $manual,
                'script' => $automated,
                'loadrunner' => $loadrunner,
                'datecreated' => date('Y-m-d H:i:s'),
                'lastupdated' => date('Y-m-d H:i:s'),
                'lastupdatedby' => $this->getUser()->getUserIdentifier(),
                'deleted' => 'N',
                'archive' => 'N',
                'codereview' => 'N',
                'ba_approval' => 'N',
                'approvedforauto' => 'N',
                'basignoff' => '',
                'uniqueid' => uniqid(),
            ]);

            return $this->redirectToRoute('app_project_tests', ['id' => $id]);
        }

        $lookups = $this->getTestLookups($id, $connection);

        return $this->render('project/test_new.html.twig', [
            'project' => $project,
            'lookups' => $lookups,
        ]);
    }

    #[Route('/project/{id}/tests/{testId}', name: 'app_project_test_detail', requirements: ['testId' => '\d+'], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function testDetail(int $id, int $testId, Connection $connection): Response
    {
        $project = $connection->fetchAssociative('SELECT * FROM project WHERE project_id = ?', [$id]);
        $test = $connection->fetchAssociative('SELECT * FROM testsuite WHERE testid = ? AND project_id = ?', [$testId, $id]);

        if (!$project || !$test) {
            throw $this->createNotFoundException('Test case not found');
        }

        return $this->render('project/test_detail.html.twig', [
            'project' => $project,
            'test' => $test,
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
            $name = trim($request->request->get('name', ''));
            $purpose = trim($request->request->get('purpose', ''));
            $comments = trim($request->request->get('comments', ''));
            $priority = $request->request->get('priority', 'Medium');
            $status = $request->request->get('status', 'New');
            $areaTested = $request->request->get('areatested', '');
            $testType = $request->request->get('testtype', 'Functional');
            $baOwner = $request->request->get('baowner', '');
            $qaOwner = $request->request->get('scripter', '');
            $tester = $request->request->get('tester', '');
            $assignedTo = $request->request->get('assignedto', '');
            $assignedBy = $request->request->get('assignedby', '');
            $dateAssigned = $request->request->get('dateassigned', '');
            $dateExpected = $request->request->get('expdatecomplete', '');
            $dateComplete = $request->request->get('actdatecomplete', '');
            $duration = trim($request->request->get('duration', ''));
            $autopass = $request->request->get('autopass') ? 'Y' : 'N';
            $emailBa = $request->request->get('email_ba_owner') ? 'Y' : 'N';
            $emailQa = $request->request->get('email_qa_owner') ? 'Y' : 'N';
            $manual = $request->request->get('steps') ? 'Y' : 'N';
            $automated = $request->request->get('script') ? 'Y' : 'N';
            $loadrunner = $request->request->get('loadrunner') ? 'Y' : 'N';

            $connection->update('testsuite', [
                'testsuitename' => $name,
                'purpose' => $purpose,
                'comments' => $comments,
                'priority' => $priority,
                'status' => $status,
                'areatested' => $areaTested,
                'testtype' => $testType,
                'baowner' => $baOwner,
                'scripter' => $qaOwner,
                'tester' => $tester,
                'assignedto' => $assignedTo,
                'assignedby' => $assignedBy,
                'dateassigned' => $dateAssigned,
                'expdatecomplete' => $dateExpected,
                'actdatecomplete' => $dateComplete,
                'duration' => $duration,
                'autopass' => $autopass,
                'email_ba_owner' => $emailBa,
                'email_qa_owner' => $emailQa,
                'steps' => $manual,
                'script' => $automated,
                'loadrunner' => $loadrunner,
                'lastupdated' => date('Y-m-d H:i:s'),
                'lastupdatedby' => $this->getUser()->getUserIdentifier(),
            ], ['testid' => $testId]);

            return $this->redirectToRoute('app_project_test_detail', ['id' => $id, 'testId' => $testId]);
        }

        $lookups = $this->getTestLookups($id, $connection);

        return $this->render('project/test_edit.html.twig', [
            'project' => $project,
            'test' => $test,
            'lookups' => $lookups,
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

    #[Route('/project/{id}/tests/{testId}/copy', name: 'app_project_test_copy', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function testCopy(int $id, int $testId, Request $request, Connection $connection): Response
    {
        $project = $connection->fetchAssociative('SELECT * FROM project WHERE project_id = ?', [$id]);
        $test = $connection->fetchAssociative('SELECT * FROM testsuite WHERE testid = ? AND project_id = ?', [$testId, $id]);

        if (!$project || !$test) {
            throw $this->createNotFoundException('Test case not found');
        }

        if ($request->isMethod('POST')) {
            $targetProjectId = (int) $request->request->get('target_project_id');
            $targetProject = $connection->fetchAssociative('SELECT * FROM project WHERE project_id = ?', [$targetProjectId]);

            if (!$targetProject) {
                $this->addFlash('error', 'Target project not found');
                return $this->redirectToRoute('app_project_test_copy', ['id' => $id, 'testId' => $testId]);
            }

            $newTestData = $test;
            unset($newTestData['testid']);
            $newTestData['project_id'] = $targetProjectId;
            $newTestData['datecreated'] = date('Y-m-d H:i:s');
            $newTestData['lastupdated'] = date('Y-m-d H:i:s');
            $newTestData['lastupdatedby'] = $this->getUser()->getUserIdentifier();
            $newTestData['uniqueid'] = uniqid();

            if ($targetProjectId === $id) {
                $newTestData['testsuitename'] .= ' - Copy';
            }

            $connection->insert('testsuite', $newTestData);
            $newTestId = (int) $connection->lastInsertId();

            $steps = $connection->fetchAllAssociative('SELECT * FROM teststep WHERE testid = ? ORDER BY teststep_number ASC', [$testId]);
            foreach ($steps as $step) {
                $newStepData = $step;
                unset($newStepData['teststepid']);
                $newStepData['testid'] = $newTestId;
                $connection->insert('teststep', $newStepData);
            }

            $this->addFlash('success', 'Test case successfully copied!');
            return $this->redirectToRoute('app_project_test_edit', ['id' => $targetProjectId, 'testId' => $newTestId]);
        }

        $projects = $connection->fetchAllAssociative('SELECT * FROM project ORDER BY project_name ASC');

        return $this->render('project/test_copy.html.twig', [
            'project' => $project,
            'test' => $test,
            'projects' => $projects,
        ]);
    }

    #[Route('/project/{id}/tests/export', name: 'app_project_test_export_select', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function testExportSelect(int $id, Request $request, Connection $connection): Response
    {
        $project = $connection->fetchAssociative('SELECT * FROM project WHERE project_id = ?', [$id]);
        if (!$project) {
            throw $this->createNotFoundException('Project not found');
        }

        if ($request->isMethod('POST')) {
            $testIds = $request->request->all('test_ids');
            if (empty($testIds)) {
                $this->addFlash('error', 'Please select at least one test case to export.');
                return $this->redirectToRoute('app_project_test_export_select', ['id' => $id]);
            }

            $output = fopen('php://temp', 'r+');
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($output, [
                'Test ID', 'Test Name', 'Type', 'Area', 'Priority', 'Status', 'Purpose', 'Comments',
                'BA Owner', 'QA Owner', 'Tester', 'Assigned To', 'Assigned By', 'Date Assigned',
                'Expected Complete', 'Actual Complete', 'Duration (mins)', 'Auto Pass',
                'Manual Steps', 'Automated Script', 'LoadRunner',
                'Step Number', 'Action', 'Inputs', 'Expected Result', 'Step Type'
            ]);

            foreach ($testIds as $testId) {
                $test = $connection->fetchAssociative('SELECT * FROM testsuite WHERE testid = ? AND project_id = ?', [(int)$testId, $id]);
                if (!$test) {
                    continue;
                }

                $steps = $connection->fetchAllAssociative('SELECT * FROM teststep WHERE testid = ? ORDER BY teststep_number ASC', [(int)$testId]);

                if (empty($steps)) {
                    fputcsv($output, [
                        'TC-' . $test['testid'],
                        $test['testsuitename'],
                        $test['testtype'],
                        $test['areatested'],
                        $test['priority'],
                        $test['status'],
                        $test['purpose'],
                        $test['comments'],
                        $test['baowner'],
                        $test['scripter'],
                        $test['tester'],
                        $test['assignedto'],
                        $test['assignedby'],
                        $test['dateassigned'],
                        $test['expdatecomplete'],
                        $test['actdatecomplete'],
                        $test['duration'],
                        $test['autopass'],
                        $test['steps'],
                        $test['script'],
                        $test['loadrunner'],
                        '', '', '', '', ''
                    ]);
                } else {
                    foreach ($steps as $index => $step) {
                        if ($index === 0) {
                            fputcsv($output, [
                                'TC-' . $test['testid'],
                                $test['testsuitename'],
                                $test['testtype'],
                                $test['areatested'],
                                $test['priority'],
                                $test['status'],
                                $test['purpose'],
                                $test['comments'],
                                $test['baowner'],
                                $test['scripter'],
                                $test['tester'],
                                $test['assignedto'],
                                $test['assignedby'],
                                $test['dateassigned'],
                                $test['expdatecomplete'],
                                $test['actdatecomplete'],
                                $test['duration'],
                                $test['autopass'],
                                $test['steps'],
                                $test['script'],
                                $test['loadrunner'],
                                $step['teststep_number'],
                                $step['action'],
                                $step['inputs'],
                                $step['expected_result'],
                                $step['steptype']
                            ]);
                        } else {
                            fputcsv($output, [
                                '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '',
                                $step['teststep_number'],
                                $step['action'],
                                $step['inputs'],
                                $step['expected_result'],
                                $step['steptype']
                            ]);
                        }
                    }
                }
            }

            rewind($output);
            $csvContent = stream_get_contents($output);
            fclose($output);

            $filename = sprintf('test_cases_export_%s_%s.csv', $project['project_name'], date('Ymd_His'));
            $filename = str_replace(' ', '_', $filename);

            $response = new Response($csvContent);
            $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
            $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', $filename));
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');

            return $response;
        }

        $tests = $connection->fetchAllAssociative('SELECT * FROM testsuite WHERE project_id = ? AND deleted = \'N\' ORDER BY testid ASC', [$id]);

        return $this->render('project/test_export_select.html.twig', [
            'project' => $project,
            'tests' => $tests,
        ]);
    }

    #[Route('/project/{id}/tests/import', name: 'app_project_test_import', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function testImport(int $id, Request $request, Connection $connection): Response
    {
        $project = $connection->fetchAssociative('SELECT * FROM project WHERE project_id = ?', [$id]);
        if (!$project) {
            throw $this->createNotFoundException('Project not found');
        }

        if ($request->isMethod('POST')) {
            $file = $request->files->get('csv_file');
            if (!$file) {
                $this->addFlash('error', 'Please select a CSV file to upload.');
                return $this->redirectToRoute('app_project_test_import', ['id' => $id]);
            }

            $path = $file->getRealPath();
            if (($handle = fopen($path, 'r')) !== false) {
                $bom = fread($handle, 3);
                if ($bom !== chr(0xEF).chr(0xBB).chr(0xBF)) {
                    rewind($handle);
                }

                $headers = fgetcsv($handle);
                if (!$headers || count($headers) < 2) {
                    $this->addFlash('error', 'Invalid CSV format. Header row missing or invalid.');
                    fclose($handle);
                    return $this->redirectToRoute('app_project_test_import', ['id' => $id]);
                }

                $currentTestId = null;
                $newCount = 0;
                $updateCount = 0;

                while (($row = fgetcsv($handle)) !== false) {
                    if (empty($row) || count($row) < 2) {
                        continue;
                    }

                    $testName = trim($row[1] ?? '');
                    if ($testName !== '') {
                        $testIdRaw = trim($row[0] ?? '');
                        $targetTestId = 0;
                        if ($testIdRaw !== '') {
                            $targetTestId = (int) filter_var($testIdRaw, FILTER_SANITIZE_NUMBER_INT);
                        }

                        $testType = trim($row[2] ?? 'Regression');
                        $areaTested = trim($row[3] ?? 'Bugs');
                        $priority = trim($row[4] ?? 'Medium');
                        $status = trim($row[5] ?? 'Ready');
                        $purpose = trim($row[6] ?? '');
                        $comments = trim($row[7] ?? '');
                        $baOwner = trim($row[8] ?? '');
                        $scripter = trim($row[9] ?? '');
                        $tester = trim($row[10] ?? '');
                        $assignedTo = trim($row[11] ?? '');
                        $assignedBy = trim($row[12] ?? '');
                        
                        $dateAssigned = trim($row[13] ?? '');
                        if ($dateAssigned === '') $dateAssigned = null;

                        $expDateComplete = trim($row[14] ?? '');
                        if ($expDateComplete === '') $expDateComplete = null;

                        $actDateComplete = trim($row[15] ?? '');
                        if ($actDateComplete === '') $actDateComplete = null;

                        $duration = ($row[16] ?? '') !== '' ? (int) $row[16] : 0;

                        $autopass = in_array(strtoupper(trim($row[17] ?? '')), ['Y', 'YES', '1', 'TRUE']) ? 'Y' : 'N';
                        $stepsEnabled = in_array(strtoupper(trim($row[18] ?? '')), ['Y', 'YES', '1', 'TRUE']) ? 'Y' : 'N';
                        $scriptEnabled = in_array(strtoupper(trim($row[19] ?? '')), ['Y', 'YES', '1', 'TRUE']) ? 'Y' : 'N';
                        $loadrunnerEnabled = in_array(strtoupper(trim($row[20] ?? '')), ['Y', 'YES', '1', 'TRUE']) ? 'Y' : 'N';

                        $existingTest = null;
                        if ($targetTestId > 0) {
                            $existingTest = $connection->fetchAssociative(
                                'SELECT testid FROM testsuite WHERE testid = ? AND project_id = ?',
                                [$targetTestId, $id]
                            );
                        }

                        $testData = [
                            'project_id' => $id,
                            'testsuitename' => $testName,
                            'testtype' => $testType,
                            'areatested' => $areaTested,
                            'priority' => $priority,
                            'status' => $status,
                            'purpose' => $purpose,
                            'comments' => $comments,
                            'baowner' => $baOwner,
                            'scripter' => $scripter,
                            'tester' => $tester,
                            'assignedto' => $assignedTo,
                            'assignedby' => $assignedBy,
                            'dateassigned' => $dateAssigned,
                            'expdatecomplete' => $expDateComplete,
                            'actdatecomplete' => $actDateComplete,
                            'duration' => $duration,
                            'autopass' => $autopass,
                            'steps' => $stepsEnabled,
                            'script' => $scriptEnabled,
                            'loadrunner' => $loadrunnerEnabled,
                            'lastupdated' => date('Y-m-d H:i:s'),
                            'lastupdatedby' => $this->getUser()->getUserIdentifier(),
                        ];

                        if ($existingTest) {
                            $connection->update('testsuite', $testData, ['testid' => $targetTestId]);
                            $currentTestId = $targetTestId;
                            $connection->executeStatement('DELETE FROM teststep WHERE testid = ?', [$targetTestId]);
                            $updateCount++;
                        } else {
                            $testData['datecreated'] = date('Y-m-d H:i:s');
                            $testData['uniqueid'] = uniqid();
                            $testData['deleted'] = 'N';
                            $connection->insert('testsuite', $testData);
                            $currentTestId = (int) $connection->lastInsertId();
                            $newCount++;
                        }
                    }

                    if ($currentTestId !== null) {
                        $stepNumberRaw = trim($row[21] ?? '');
                        if ($stepNumberRaw !== '') {
                            $stepNumber = (int) $stepNumberRaw;
                            $action = trim($row[22] ?? '');
                            $inputs = trim($row[23] ?? '');
                            $expectedResult = trim($row[24] ?? '');
                            $stepType = trim($row[25] ?? 'Manual');

                            $connection->insert('teststep', [
                                'testid' => $currentTestId,
                                'teststep_number' => $stepNumber,
                                'action' => $action,
                                'inputs' => $inputs,
                                'expected_result' => $expectedResult,
                                'steptype' => $stepType
                            ]);
                        }
                    }
                }

                fclose($handle);
                $this->addFlash('success', sprintf(
                    'CSV Import successful! Created %d new test cases, updated %d existing test cases.',
                    $newCount,
                    $updateCount
                ));
            } else {
                $this->addFlash('error', 'Unable to open uploaded file.');
            }

            return $this->redirectToRoute('app_project_tests', ['id' => $id]);
        }

        return $this->render('project/test_import.html.twig', [
            'project' => $project,
        ]);
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
        $sql = 'SELECT b.*, bc.categoryname AS category_name, bco.componentname AS component_name 
                FROM bug b 
                LEFT JOIN bugcategory bc ON b.category = bc.categoryid 
                LEFT JOIN bugcomponent bco ON b.component = bco.componentid 
                WHERE b.projectid = ?';

        if ($search) {
            $sql .= ' AND (b.summary ILIKE ? OR b.description ILIKE ?)';
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
        }

        $sql .= ' ORDER BY b.bugid DESC';

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
            SELECT t.*, assoc.teststatus, assoc.assignedto, assoc.testset_testsuite_associd, assoc.comments as run_comments, assoc.root_cause
            FROM testsuite t
            JOIN testset_testsuite_assoc assoc ON t.testid = assoc.testid
            WHERE assoc.testsetid = ?
            ORDER BY t.testid ASC
        ', [$runId]);

        foreach ($tests as &$t) {
            $t['steps'] = $connection->fetchAllAssociative('
                SELECT * FROM teststep WHERE testid = ? ORDER BY teststep_number ASC
            ', [$t['testid']]);

            // Fetch the latest execution run session for this test in this test run
            $latestRun = $connection->fetchAssociative('
                SELECT ts_uniquerunid, test_run_comment, os, environment, root_cause 
                FROM testsuiteresults 
                WHERE testsetid = ? AND testid = ? 
                ORDER BY testsuiteresultsid DESC LIMIT 1
            ', [$runId, $t['testid']]);

            if ($latestRun) {
                $t['latest_run'] = $latestRun;
                // Fetch previous step results recorded in this session
                $stepResults = $connection->fetchAllAssociative('
                    SELECT stepnumber, actualresult, teststatus 
                    FROM verifyresults 
                    WHERE ts_uniquerunid = ?
                ', [$latestRun['ts_uniquerunid']]);
                $stepMap = [];
                foreach ($stepResults as $sr) {
                    $stepMap[$sr['stepnumber']] = $sr;
                }
                foreach ($t['steps'] as &$st) {
                    $num = (string)$st['teststep_number'];
                    if (isset($stepMap[$num])) {
                        $st['actual_result'] = $stepMap[$num]['actualresult'];
                        $st['step_status'] = $stepMap[$num]['teststatus'];
                    }
                }
                unset($st);
            }
        }
        unset($t);

        // Fetch active users for assignment dropdowns
        $users = $this->getProjectUsers($id, $connection);

        return $this->render('project/test_run_execute.html.twig', [
            'project' => $project,
            'testRun' => $testRun,
            'tests' => $tests,
            'users' => $users,
        ]);
    }

    #[Route('/project/{id}/test-runs/{runId}/update-status/{assocId}', name: 'app_project_test_run_update_status', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function updateTestRunStatus(int $id, int $runId, int $assocId, Request $request, Connection $connection): Response
    {
        $status = $request->request->get('status', 'Passed');
        $connection->update('testset_testsuite_assoc', [
            'teststatus' => $status,
        ], ['testset_testsuite_associd' => $assocId]);

        return $this->redirectToRoute('app_project_test_run_execute', ['id' => $id, 'runId' => $runId]);
    }

    #[Route('/project/{id}/test-runs/{runId}/save-results/{assocId}', name: 'app_project_test_run_save_results', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function saveTestRunResults(int $id, int $runId, int $assocId, Request $request, Connection $connection): Response
    {
        $project = $connection->fetchAssociative('SELECT * FROM project WHERE project_id = ?', [$id]);
        $assoc = $connection->fetchAssociative('SELECT * FROM testset_testsuite_assoc WHERE testset_testsuite_associd = ?', [$assocId]);

        if (!$project || !$assoc) {
            throw $this->createNotFoundException('Test association not found');
        }

        $testId = (int)$assoc['testid'];
        $testName = $connection->fetchOne('SELECT testsuitename FROM testsuite WHERE testid = ?', [$testId]);
        $user = $request->request->get('assigned_to', $this->getUser() ? $this->getUser()->getUserIdentifier() : 'admin');

        $status = $request->request->get('test_status', 'Passed');
        $comments = $request->request->get('comments', '');
        $duration = (int)$request->request->get('duration', 0);
        $os = $request->request->get('os', 'Windows');
        $environment = $request->request->get('environment', 'QA');
        $rootCause = $request->request->get('root_cause', '');

        $timeFinished = date('Y-m-d H:i:s');
        $timeStarted = date('Y-m-d H:i:s', time() - ($duration * 60));
        $runUniqueId = 'M' . time() . rand(100, 999);
        $finished = ($status === 'Passed') ? 1 : 0;

        // 1. Update testset_testsuite_assoc
        $connection->update('testset_testsuite_assoc', [
            'teststatus' => $status,
            'comments' => $comments,
            'root_cause' => $rootCause,
            'finished' => $finished,
            'logtimestamp' => $timeFinished,
            'assignedto' => $user,
        ], ['testset_testsuite_associd' => $assocId]);

        // 2. Insert into testsuiteresults
        $connection->insert('testsuiteresults', [
            'testsetid' => $runId,
            'project_id' => $id,
            'testid' => $testId,
            'logtimestamp' => $timeFinished,
            'teststatus' => $status,
            'assigned_to' => $user,
            'root_cause' => $rootCause,
            'test_run_comment' => $comments,
            'started' => time() - ($duration * 60),
            'finished' => time(),
            'cvsversion' => 1.0,
            'checkedforautopass' => 'N',
            'os' => $os,
            'sp' => '',
            'nnumberid' => '',
            'userid' => $user,
            'machinename' => 'RTH-MODERN-HOST',
            'testsuite' => $testName ?: 'Test Case',
            'testpath' => '',
            'environment' => $environment,
            'runid' => 'RUN-' . $runId,
            'ts_uniquerunid' => $runUniqueId,
            'timestarted' => $timeStarted,
            'timefinished' => $timeFinished,
        ]);

        // 3. Insert individual step results into verifyresults
        $steps = $connection->fetchAllAssociative('SELECT * FROM teststep WHERE testid = ? ORDER BY teststep_number ASC', [$testId]);
        foreach ($steps as $step) {
            $stepId = $step['teststepid'];
            $actualResult = $request->request->get("actual_result_$stepId", '');
            $stepStatus = $request->request->get("step_status_$stepId", '');

            $connection->insert('verifyresults', [
                'logtimestamp' => $timeFinished,
                'teststatus' => $stepStatus,
                'linenumber' => 0,
                'defect_id' => 0,
                'totalphymem' => 0,
                'freephymem' => 0,
                'totalvirmem' => 0,
                'freevirmem' => 0,
                'curmemutil' => 0,
                'totalpagefile' => 0,
                'freepagefile' => 0,
                'custom_5' => '',
                'custom_3' => '',
                'custom_1' => '',
                'custom_2' => '',
                'custom_6' => '',
                'custom_4' => '',
                'actualresult' => $actualResult,
                '"comment"' => '',
                'action' => $step['action'] ?? '',
                'expectedresult' => $step['expected_result'] ?? '',
                '"window"' => '',
                '"object"' => '',
                'objtype' => '',
                'stepnumber' => (string)$step['teststep_number'],
                'ts_uniquerunid' => $runUniqueId,
                'timestamp' => $timeFinished,
            ]);
        }

        return $this->redirectToRoute('app_project_test_run_execute', ['id' => $id, 'runId' => $runId]);
    }

    /**
     * Helper to get active users for a project, auto-associating any unassociated active users.
     */
    private function getProjectUsers(int $projectId, Connection $connection): array
    {
        $unassociatedUsers = $connection->fetchAllAssociative('
            SELECT u.user_id FROM rth_user u 
            WHERE u.deleted = \'N\' AND u.user_id NOT IN (
                SELECT pua.user_id FROM project_user_assoc pua WHERE pua.project_id = ?
            )
        ', [$projectId]);

        foreach ($unassociatedUsers as $u) {
            $connection->insert('project_user_assoc', [
                'project_id' => $projectId,
                'user_id' => $u['user_id'],
                'delete_rights' => 'Y',
                'email_testset' => 'Y',
                'email_discussion' => 'Y',
                'email_new_bug' => 'Y',
                'email_update_bug' => 'Y',
                'email_assigned_bug' => 'Y',
                'email_bugnote_bug' => 'Y',
                'email_status_bug' => 'Y',
                'qa_tester' => 'Y',
                'ba_owner' => 'Y',
                'user_rights' => 10,
            ]);
        }

        return $connection->fetchAllAssociative('
            SELECT u.username FROM rth_user u 
            JOIN project_user_assoc pua ON u.user_id = pua.user_id 
            WHERE pua.project_id = ? AND u.deleted = \'N\' 
            ORDER BY u.username
        ', [$projectId]);
    }

    /**
     * Helper to fetch all lookup data needed by requirement forms.
     */
    private function getRequirementLookups(int $projectId, Connection $connection): array
    {
        $docTypes = $connection->fetchAllAssociative(
            'SELECT reqdoctypeid, reqdoctypename FROM requirementdocumenttype WHERE projectid = ? ORDER BY reqdoctypename', [$projectId]
        );
        if (empty($docTypes)) {
            $connection->insert('requirementdocumenttype', ['projectid' => $projectId, 'reqdoctypename' => 'Use Case', 'rootdocument' => 'N']);
            $connection->insert('requirementdocumenttype', ['projectid' => $projectId, 'reqdoctypename' => 'Func Spec', 'rootdocument' => 'N']);
            $connection->insert('requirementdocumenttype', ['projectid' => $projectId, 'reqdoctypename' => 'Tech Spec', 'rootdocument' => 'N']);
            $docTypes = $connection->fetchAllAssociative(
                'SELECT reqdoctypeid, reqdoctypename FROM requirementdocumenttype WHERE projectid = ? ORDER BY reqdoctypename', [$projectId]
            );
        }

        $areas = $connection->fetchAllAssociative(
            'SELECT reqareacoverageid, areacoverage FROM requirementareacoverage WHERE projectid = ? ORDER BY areacoverage', [$projectId]
        );
        if (empty($areas)) {
            $connection->insert('requirementareacoverage', ['projectid' => $projectId, 'areacoverage' => 'Requirements']);
            $connection->insert('requirementareacoverage', ['projectid' => $projectId, 'areacoverage' => 'Bugs']);
            $connection->insert('requirementareacoverage', ['projectid' => $projectId, 'areacoverage' => 'Tests']);
            $connection->insert('requirementareacoverage', ['projectid' => $projectId, 'areacoverage' => 'Test Results']);
            $areas = $connection->fetchAllAssociative(
                'SELECT reqareacoverageid, areacoverage FROM requirementareacoverage WHERE projectid = ? ORDER BY areacoverage', [$projectId]
            );
        }

        $functionalities = $connection->fetchAllAssociative(
            'SELECT functionalityid, functionalityname FROM requirementfunctionality WHERE projectid = ? ORDER BY functionalityname', [$projectId]
        );
        if (empty($functionalities)) {
            $connection->insert('requirementfunctionality', ['projectid' => $projectId, 'functionalityname' => 'User Mgmt']);
            $connection->insert('requirementfunctionality', ['projectid' => $projectId, 'functionalityname' => 'Run Level']);
            $connection->insert('requirementfunctionality', ['projectid' => $projectId, 'functionalityname' => 'Permissions']);
            $connection->insert('requirementfunctionality', ['projectid' => $projectId, 'functionalityname' => 'Group Mgmt']);
            $functionalities = $connection->fetchAllAssociative(
                'SELECT functionalityid, functionalityname FROM requirementfunctionality WHERE projectid = ? ORDER BY functionalityname', [$projectId]
            );
        }

        $releases = $connection->fetchAllAssociative(
            'SELECT r.releaseid, r.releasename FROM release_tbl r WHERE r.project_id = ? ORDER BY r.releasename', [$projectId]
        );
        $users = $this->getProjectUsers($projectId, $connection);
        $allRequirements = $connection->fetchAllAssociative(
            'SELECT reqid, reqname FROM requirement WHERE project_id = ? ORDER BY reqname', [$projectId]
        );

        return [
            'docTypes' => $docTypes,
            'areas' => $areas,
            'functionalities' => $functionalities,
            'releases' => $releases,
            'users' => $users,
            'allRequirements' => $allRequirements,
        ];
    }

    private function getTestLookups(int $projectId, Connection $connection): array
    {
        $areas = $connection->fetchAllAssociative(
            'SELECT areatestedid, areatestedname FROM testarea WHERE project_id = ? ORDER BY areatestedname', [$projectId]
        );
        if (empty($areas)) {
            $connection->insert('testarea', ['project_id' => $projectId, 'areatestedname' => 'Requirements']);
            $connection->insert('testarea', ['project_id' => $projectId, 'areatestedname' => 'Tests']);
            $connection->insert('testarea', ['project_id' => $projectId, 'areatestedname' => 'Test Results']);
            $connection->insert('testarea', ['project_id' => $projectId, 'areatestedname' => 'Bugs']);
            $connection->insert('testarea', ['project_id' => $projectId, 'areatestedname' => 'Security']);
            $areas = $connection->fetchAllAssociative(
                'SELECT areatestedid, areatestedname FROM testarea WHERE project_id = ? ORDER BY areatestedname', [$projectId]
            );
        }

        $types = $connection->fetchAllAssociative(
            'SELECT testtypeid, testtype FROM testtype WHERE project_id = ? ORDER BY testtype', [$projectId]
        );
        if (empty($types)) {
            $connection->insert('testtype', ['project_id' => $projectId, 'testtype' => 'Smoke']);
            $connection->insert('testtype', ['project_id' => $projectId, 'testtype' => 'Regression']);
            $connection->insert('testtype', ['project_id' => $projectId, 'testtype' => 'Performance']);
            $connection->insert('testtype', ['project_id' => $projectId, 'testtype' => 'Functional']);
            $connection->insert('testtype', ['project_id' => $projectId, 'testtype' => 'UI/UX']);
            $types = $connection->fetchAllAssociative(
                'SELECT testtypeid, testtype FROM testtype WHERE project_id = ? ORDER BY testtype', [$projectId]
            );
        }

        $users = $this->getProjectUsers($projectId, $connection);

        $statuses = [
            'New', 'Assigned', 'WIP', 'Ready for Review', 'Completed',
            'Rework', 'Review Test Case', 'Review Requirement',
            'Draft', 'Ready', 'Passed', 'Failed', 'Retired'
        ];

        return [
            'areas' => $areas,
            'types' => $types,
            'users' => $users,
            'statuses' => $statuses,
        ];
    }

    private function getBugLookups(int $projectId, Connection $connection): array
    {
        $categories = $connection->fetchAllAssociative(
            'SELECT categoryid, categoryname FROM bugcategory WHERE projectid = ? ORDER BY categoryname', [$projectId]
        );
        if (empty($categories)) {
            $connection->insert('bugcategory', ['projectid' => $projectId, 'categoryname' => 'Defect']);
            $connection->insert('bugcategory', ['projectid' => $projectId, 'categoryname' => 'Feature Request']);
            $connection->insert('bugcategory', ['projectid' => $projectId, 'categoryname' => 'Enhancement']);
            $connection->insert('bugcategory', ['projectid' => $projectId, 'categoryname' => 'Performance']);
            $connection->insert('bugcategory', ['projectid' => $projectId, 'categoryname' => 'Security']);
            $categories = $connection->fetchAllAssociative(
                'SELECT categoryid, categoryname FROM bugcategory WHERE projectid = ? ORDER BY categoryname', [$projectId]
            );
        }

        $components = $connection->fetchAllAssociative(
            'SELECT componentid, componentname FROM bugcomponent WHERE projectid = ? ORDER BY componentname', [$projectId]
        );
        if (empty($components)) {
            $connection->insert('bugcomponent', ['projectid' => $projectId, 'componentname' => 'Authentication']);
            $connection->insert('bugcomponent', ['projectid' => $projectId, 'componentname' => 'Database']);
            $connection->insert('bugcomponent', ['projectid' => $projectId, 'componentname' => 'Reporting']);
            $connection->insert('bugcomponent', ['projectid' => $projectId, 'componentname' => 'UI/UX']);
            $connection->insert('bugcomponent', ['projectid' => $projectId, 'componentname' => 'API']);
            $components = $connection->fetchAllAssociative(
                'SELECT componentid, componentname FROM bugcomponent WHERE projectid = ? ORDER BY componentname', [$projectId]
            );
        }

        $releases = $connection->fetchAllAssociative(
            'SELECT releaseid, releasename FROM release_tbl WHERE project_id = ? ORDER BY releasename', [$projectId]
        );

        $users = $this->getProjectUsers($projectId, $connection);

        $verifications = $connection->fetchAllAssociative(
            'SELECT verifyresultsid, ts_uniquerunid, stepnumber, action, expectedresult, actualresult FROM verifyresults ORDER BY verifyresultsid DESC LIMIT 100'
        );

        return [
            'categories' => $categories,
            'components' => $components,
            'releases' => $releases,
            'users' => $users,
            'verifications' => $verifications,
        ];
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
            $type = (int)$request->request->get('type');
            $status = $request->request->get('status', 'New');
            $areaCovered = (int)$request->request->get('area_covered', 0);
            $parentReq = (int)$request->request->get('parent_req', 0);
            $assignedTo = $request->request->get('assigned_to', '');
            $defectId = (int)$request->request->get('defect_id', 0);
            $reasonForChange = $request->request->get('reason_for_change', '');
            $version = $request->request->get('version', '1.0');
            $recordOrFile = $request->request->get('record_or_file', 'R');
            $assignRelease = (int)$request->request->get('assign_release', 0);
            $functionalityIds = $request->request->all('functionality');

            // Handle file upload
            $uploadedFilename = '';
            if ($recordOrFile === 'F') {
                $uploadedFile = $request->files->get('upload_file');
                if ($uploadedFile && $uploadedFile->isValid()) {
                    $shareDir = $this->getParameter('kernel.project_dir') . '/var/share/requirements';
                    if (!is_dir($shareDir)) {
                        mkdir($shareDir, 0777, true);
                    }
                    $uploadedFilename = uniqid() . '_' . $uploadedFile->getClientOriginalName();
                    $uploadedFile->move($shareDir, $uploadedFilename);
                }
            }

            $connection->insert('requirement', [
                'project_id' => $id,
                'reqname' => $name,
                'priority' => $priority,
                'type' => $type,
                'areacovered' => $areaCovered,
                'parent' => $parentReq,
                'recordorfile' => $recordOrFile,
                'datecreated' => date('Y-m-d H:i:s'),
                'logtimestamp' => date('Y-m-d H:i:s'),
                'lastupdated' => date('Y-m-d H:i:s'),
            ]);

            $reqId = $connection->lastInsertId();

            $connection->insert('requirementversion', [
                'reqid' => $reqId,
                'version' => $version,
                'latest' => 'Y',
                'status' => $status,
                'author' => $this->getUser()->getUserIdentifier(),
                'detail' => ($recordOrFile === 'R') ? $detail : '',
                'filename' => $uploadedFilename,
                'assignedto' => $assignedTo,
                'defect_id' => $defectId,
                'reasonforchange' => $reasonForChange,
                'timestamp' => date('Y-m-d H:i:s'),
                'lastupdated' => date('Y-m-d H:i:s'),
                'lastupdatedby' => $this->getUser()->getUserIdentifier(),
            ]);

            // Save functionality associations
            foreach ($functionalityIds as $funcId) {
                if ($funcId) {
                    $connection->insert('requirementfunctionality_assoc', [
                        'requirementid' => $reqId,
                        'requirementfunctionalityid' => (int)$funcId,
                    ]);
                }
            }

            // Save release association
            if ($assignRelease > 0) {
                $reqVersionId = $connection->lastInsertId();
                $connection->insert('requirementversion_release_assoc', [
                    'requirementversionid' => $reqVersionId,
                    'releaseid' => $assignRelease,
                ]);
            }

            return $this->redirectToRoute('app_project_requirements', ['id' => $id]);
        }

        $lookups = $this->getRequirementLookups($id, $connection);

        return $this->render('project/requirement_new.html.twig', array_merge(
            ['project' => $project],
            $lookups
        ));
    }

    #[Route('/project/{id}/requirements/{reqId}', name: 'app_project_requirement_detail', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function requirementDetail(int $id, int $reqId, Connection $connection): Response
    {
        $project = $connection->fetchAssociative('SELECT * FROM project WHERE project_id = ?', [$id]);
        $requirement = $connection->fetchAssociative('
            SELECT r.*, rv.detail, rv.status, rv.version, rv.author, rv.assignedto, 
                   rv.defect_id, rv.reasonforchange, rv.filename, rv.reqversionid,
                   rv.timestamp as version_created,
                   rdt.reqdoctypename AS doc_type_name,
                   rac.areacoverage AS area_name
            FROM requirement r 
            LEFT JOIN requirementversion rv ON r.reqid = rv.reqid AND rv.latest = \'Y\' 
            LEFT JOIN requirementdocumenttype rdt ON r.type = rdt.reqdoctypeid
            LEFT JOIN requirementareacoverage rac ON r.areacovered = rac.reqareacoverageid
            WHERE r.reqid = ? AND r.project_id = ?
        ', [$reqId, $id]);

        if (!$project || !$requirement) {
            throw $this->createNotFoundException('Requirement not found');
        }

        // Get functionality associations
        $functionalities = $connection->fetchAllAssociative('
            SELECT rf.functionalityname
            FROM requirementfunctionality_assoc rfa
            JOIN requirementfunctionality rf ON rfa.requirementfunctionalityid = rf.functionalityid
            WHERE rfa.requirementid = ?
        ', [$reqId]);

        // Get parent requirement name (if exists)
        $parentReq = null;
        if ($requirement['parent'] > 0) {
            $parentReq = $connection->fetchAssociative(
                'SELECT reqid, reqname FROM requirement WHERE reqid = ?', [$requirement['parent']]
            );
        }

        // Get child requirements
        $children = $connection->fetchAllAssociative(
            'SELECT reqid, reqname FROM requirement WHERE parent = ? ORDER BY reqid', [$reqId]
        );

        // Get associated releases
        $releases = [];
        if ($requirement['reqversionid']) {
            $releases = $connection->fetchAllAssociative('
                SELECT r.releaseid, r.releasename
                FROM requirementversion_release_assoc rra
                JOIN release_tbl r ON rra.releaseid = r.releaseid
                WHERE rra.requirementversionid = ?
            ', [$requirement['reqversionid']]);
        }

        // Get associated tests
        $tests = $connection->fetchAllAssociative('
            SELECT t.testid, t.testsuitename, tra.percentcovered
            FROM testsuite_requirement_assoc tra
            JOIN testsuite t ON tra.testid = t.testid
            WHERE tra.reqid = ?
        ', [$reqId]);

        // Get discussion count
        $discussions = $connection->fetchAllAssociative(
            'SELECT discussionid, discsubject, status, author, date FROM discussion WHERE reqid = ? ORDER BY discussionid DESC LIMIT 5', [$reqId]
        );
        $discussionCount = $connection->fetchOne('SELECT COUNT(*) FROM discussion WHERE reqid = ?', [$reqId]);

        // Get available releases for inline add
        $availableReleases = $connection->fetchAllAssociative(
            'SELECT releaseid, releasename FROM release_tbl WHERE project_id = ? ORDER BY releasename', [$id]
        );

        // Get current release IDs for removal
        $releaseIds = [];
        if ($requirement['reqversionid']) {
            $releaseIds = $connection->fetchFirstColumn(
                'SELECT releaseid FROM requirementversion_release_assoc WHERE requirementversionid = ?', [$requirement['reqversionid']]
            );
        }

        return $this->render('project/requirement_detail.html.twig', [
            'project' => $project,
            'requirement' => $requirement,
            'functionalities' => $functionalities,
            'parentReq' => $parentReq,
            'children' => $children,
            'releases' => $releases,
            'tests' => $tests,
            'discussions' => $discussions,
            'discussionCount' => $discussionCount,
            'availableReleases' => $availableReleases,
            'releaseIds' => $releaseIds,
        ]);
    }

    #[Route('/project/{id}/requirements/{reqId}/edit', name: 'app_project_requirement_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function requirementEdit(int $id, int $reqId, Request $request, Connection $connection): Response
    {
        $project = $connection->fetchAssociative('SELECT * FROM project WHERE project_id = ?', [$id]);
        $requirement = $connection->fetchAssociative('
            SELECT r.*, rv.detail, rv.status, rv.version, rv.author, rv.assignedto, 
                   rv.defect_id, rv.reasonforchange, rv.filename, rv.reqversionid
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
            $type = (int)$request->request->get('type');
            $status = $request->request->get('status');
            $areaCovered = (int)$request->request->get('area_covered', 0);
            $parentReq = (int)$request->request->get('parent_req', 0);
            $assignedTo = $request->request->get('assigned_to', '');
            $defectId = (int)$request->request->get('defect_id', 0);
            $reasonForChange = $request->request->get('reason_for_change', '');
            $assignRelease = (int)$request->request->get('assign_release', 0);
            $functionalityIds = $request->request->all('functionality');

            // Handle file upload replacement
            $uploadedFilename = $requirement['filename'] ?? '';
            $uploadedFile = $request->files->get('upload_file');
            if ($uploadedFile && $uploadedFile->isValid()) {
                $shareDir = $this->getParameter('kernel.project_dir') . '/var/share/requirements';
                if (!is_dir($shareDir)) {
                    mkdir($shareDir, 0777, true);
                }
                $uploadedFilename = uniqid() . '_' . $uploadedFile->getClientOriginalName();
                $uploadedFile->move($shareDir, $uploadedFilename);
            }

            $connection->update('requirement', [
                'reqname' => $name,
                'priority' => $priority,
                'type' => $type,
                'areacovered' => $areaCovered,
                'parent' => $parentReq,
                'lastupdated' => date('Y-m-d H:i:s'),
            ], ['reqid' => $reqId]);

            $connection->update('requirementversion', [
                'detail' => $detail,
                'status' => $status,
                'assignedto' => $assignedTo,
                'defect_id' => $defectId,
                'reasonforchange' => $reasonForChange,
                'filename' => $uploadedFilename,
                'lastupdated' => date('Y-m-d H:i:s'),
                'lastupdatedby' => $this->getUser()->getUserIdentifier(),
            ], ['reqid' => $reqId, 'latest' => 'Y']);

            // Update functionality associations - delete old, insert new
            $connection->delete('requirementfunctionality_assoc', ['requirementid' => $reqId]);
            foreach ($functionalityIds as $funcId) {
                if ($funcId) {
                    $connection->insert('requirementfunctionality_assoc', [
                        'requirementid' => $reqId,
                        'requirementfunctionalityid' => (int)$funcId,
                    ]);
                }
            }

            // Update release association
            if ($requirement['reqversionid']) {
                $connection->delete('requirementversion_release_assoc', ['requirementversionid' => $requirement['reqversionid']]);
                if ($assignRelease > 0) {
                    $connection->insert('requirementversion_release_assoc', [
                        'requirementversionid' => $requirement['reqversionid'],
                        'releaseid' => $assignRelease,
                    ]);
                }
            }

            return $this->redirectToRoute('app_project_requirement_detail', ['id' => $id, 'reqId' => $reqId]);
        }

        // Fetch current functionality selections
        $selectedFunctionalities = $connection->fetchFirstColumn(
            'SELECT requirementfunctionalityid FROM requirementfunctionality_assoc WHERE requirementid = ?', [$reqId]
        );

        // Fetch current release selection
        $selectedRelease = 0;
        if ($requirement['reqversionid']) {
            $selectedRelease = $connection->fetchOne(
                'SELECT releaseid FROM requirementversion_release_assoc WHERE requirementversionid = ?', [$requirement['reqversionid']]
            ) ?: 0;
        }

        $lookups = $this->getRequirementLookups($id, $connection);

        return $this->render('project/requirement_edit.html.twig', array_merge(
            [
                'project' => $project,
                'requirement' => $requirement,
                'selectedFunctionalities' => $selectedFunctionalities,
                'selectedRelease' => $selectedRelease,
            ],
            $lookups
        ));
    }

    #[Route('/project/{id}/requirements/{reqId}/delete', name: 'app_project_requirement_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function requirementDelete(int $id, int $reqId, Connection $connection): Response
    {
        // Delete associations first
        $connection->delete('requirementfunctionality_assoc', ['requirementid' => $reqId]);
        $versionIds = $connection->fetchFirstColumn('SELECT reqversionid FROM requirementversion WHERE reqid = ?', [$reqId]);
        foreach ($versionIds as $vId) {
            $connection->delete('requirementversion_release_assoc', ['requirementversionid' => $vId]);
        }
        // Delete versions
        $connection->delete('requirementversion', ['reqid' => $reqId]);
        // Clear parent references
        $connection->executeStatement('UPDATE requirement SET parent = 0 WHERE parent = ?', [$reqId]);
        // Delete requirement
        $connection->delete('requirement', ['reqid' => $reqId, 'project_id' => $id]);

        return $this->redirectToRoute('app_project_requirements', ['id' => $id]);
    }

    #[Route('/project/{id}/requirements/{reqId}/download', name: 'app_project_requirement_download', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function requirementFileDownload(int $id, int $reqId, Connection $connection): Response
    {
        $requirement = $connection->fetchAssociative('
            SELECT rv.filename
            FROM requirementversion rv
            JOIN requirement r ON rv.reqid = r.reqid
            WHERE r.reqid = ? AND r.project_id = ? AND rv.latest = \'Y\'
        ', [$reqId, $id]);

        if (!$requirement || empty($requirement['filename'])) {
            throw $this->createNotFoundException('File not found');
        }

        $filePath = $this->getParameter('kernel.project_dir') . '/var/share/requirements/' . $requirement['filename'];
        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('File not found on disk');
        }

        // Extract original filename (remove uniqid prefix)
        $originalName = $requirement['filename'];
        if (strpos($originalName, '_') !== false) {
            $originalName = substr($originalName, strpos($originalName, '_') + 1);
        }

        return new Response(
            file_get_contents($filePath),
            200,
            [
                'Content-Type' => mime_content_type($filePath) ?: 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="' . $originalName . '"',
            ]
        );
    }

    #[Route('/project/{id}/requirements/{reqId}/history', name: 'app_project_requirement_history', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function requirementVersionHistory(int $id, int $reqId, Connection $connection): Response
    {
        $project = $connection->fetchAssociative('SELECT * FROM project WHERE project_id = ?', [$id]);
        $requirement = $connection->fetchAssociative('SELECT * FROM requirement WHERE reqid = ? AND project_id = ?', [$reqId, $id]);

        if (!$project || !$requirement) {
            throw $this->createNotFoundException('Requirement not found');
        }

        $versions = $connection->fetchAllAssociative('
            SELECT rv.*, 
                   (SELECT string_agg(r.releasename, \', \') 
                    FROM requirementversion_release_assoc rra 
                    JOIN release_tbl r ON rra.releaseid = r.releaseid 
                    WHERE rra.requirementversionid = rv.reqversionid) AS release_names
            FROM requirementversion rv
            WHERE rv.reqid = ?
            ORDER BY rv.reqversionid DESC
        ', [$reqId]);

        return $this->render('project/requirement_version_history.html.twig', [
            'project' => $project,
            'requirement' => $requirement,
            'versions' => $versions,
        ]);
    }

    #[Route('/project/{id}/requirements/{reqId}/version/{versionId}', name: 'app_project_requirement_version_view', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function requirementVersionView(int $id, int $reqId, int $versionId, Connection $connection): Response
    {
        $project = $connection->fetchAssociative('SELECT * FROM project WHERE project_id = ?', [$id]);
        $requirement = $connection->fetchAssociative('SELECT * FROM requirement WHERE reqid = ? AND project_id = ?', [$reqId, $id]);
        $version = $connection->fetchAssociative('SELECT * FROM requirementversion WHERE reqversionid = ? AND reqid = ?', [$versionId, $reqId]);

        if (!$project || !$requirement || !$version) {
            throw $this->createNotFoundException('Version not found');
        }

        // Get the latest version for comparison
        $latestVersion = $connection->fetchAssociative(
            'SELECT version FROM requirementversion WHERE reqid = ? AND latest = \'Y\'', [$reqId]
        );

        return $this->render('project/requirement_version_view.html.twig', [
            'project' => $project,
            'requirement' => $requirement,
            'version' => $version,
            'latestVersionNum' => $latestVersion ? $latestVersion['version'] : '?',
        ]);
    }

    #[Route('/project/{id}/requirements/{reqId}/add-version', name: 'app_project_requirement_add_version', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function requirementAddVersion(int $id, int $reqId, Request $request, Connection $connection): Response
    {
        $project = $connection->fetchAssociative('SELECT * FROM project WHERE project_id = ?', [$id]);
        $requirement = $connection->fetchAssociative('SELECT * FROM requirement WHERE reqid = ? AND project_id = ?', [$reqId, $id]);

        if (!$project || !$requirement) {
            throw $this->createNotFoundException('Requirement not found');
        }

        // Get current latest version
        $currentVersion = $connection->fetchAssociative(
            'SELECT * FROM requirementversion WHERE reqid = ? AND latest = \'Y\'', [$reqId]
        );

        if ($request->isMethod('POST')) {
            $version = $request->request->get('version');
            $status = $request->request->get('status', 'New');
            $detail = $request->request->get('detail', '');
            $assignedTo = $request->request->get('assigned_to', '');
            $defectId = (int)$request->request->get('defect_id', 0);
            $reasonForChange = $request->request->get('reason_for_change', '');
            $assignRelease = (int)$request->request->get('assign_release', 0);
            $priority = $request->request->get('priority', $requirement['priority']);

            // Handle file upload
            $uploadedFilename = '';
            if ($requirement['recordorfile'] === 'F') {
                $uploadedFile = $request->files->get('upload_file');
                if ($uploadedFile && $uploadedFile->isValid()) {
                    $shareDir = $this->getParameter('kernel.project_dir') . '/var/share/requirements';
                    if (!is_dir($shareDir)) {
                        mkdir($shareDir, 0777, true);
                    }
                    $uploadedFilename = uniqid() . '_' . $uploadedFile->getClientOriginalName();
                    $uploadedFile->move($shareDir, $uploadedFilename);
                }
            }

            // Mark current version as non-latest
            $connection->executeStatement(
                'UPDATE requirementversion SET latest = \'N\' WHERE reqid = ? AND latest = \'Y\'', [$reqId]
            );

            // Insert new version
            $connection->insert('requirementversion', [
                'reqid' => $reqId,
                'version' => $version,
                'latest' => 'Y',
                'status' => $status,
                'author' => $this->getUser()->getUserIdentifier(),
                'detail' => ($requirement['recordorfile'] === 'R') ? $detail : '',
                'filename' => $uploadedFilename,
                'assignedto' => $assignedTo,
                'defect_id' => $defectId,
                'reasonforchange' => $reasonForChange,
                'timestamp' => date('Y-m-d H:i:s'),
                'lastupdated' => date('Y-m-d H:i:s'),
                'lastupdatedby' => $this->getUser()->getUserIdentifier(),
            ]);

            // Update requirement priority and timestamp
            $connection->update('requirement', [
                'priority' => $priority,
                'lastupdated' => date('Y-m-d H:i:s'),
            ], ['reqid' => $reqId]);

            // Save release association for new version
            if ($assignRelease > 0) {
                $newVersionId = $connection->lastInsertId();
                $connection->insert('requirementversion_release_assoc', [
                    'requirementversionid' => $newVersionId,
                    'releaseid' => $assignRelease,
                ]);
            }

            return $this->redirectToRoute('app_project_requirement_detail', ['id' => $id, 'reqId' => $reqId]);
        }

        // Calculate next version number
        $nextVersion = '1.0';
        if ($currentVersion) {
            $parts = explode('.', $currentVersion['version']);
            if (count($parts) >= 2) {
                $parts[count($parts) - 1] = (int)$parts[count($parts) - 1] + 1;
                $nextVersion = implode('.', $parts);
            } else {
                $nextVersion = ((float)$currentVersion['version'] + 0.1);
                $nextVersion = number_format($nextVersion, 1);
            }
        }

        $lookups = $this->getRequirementLookups($id, $connection);

        return $this->render('project/requirement_add_version.html.twig', array_merge(
            [
                'project' => $project,
                'requirement' => $requirement,
                'currentVersion' => $currentVersion,
                'nextVersion' => $nextVersion,
            ],
            $lookups
        ));
    }

    #[Route('/project/{id}/requirements/{reqId}/lock', name: 'app_project_requirement_lock', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function requirementLock(int $id, int $reqId, Connection $connection): Response
    {
        $requirement = $connection->fetchAssociative('SELECT * FROM requirement WHERE reqid = ? AND project_id = ?', [$reqId, $id]);

        if (!$requirement) {
            throw $this->createNotFoundException('Requirement not found');
        }

        $connection->update('requirement', [
            'lockedby' => $this->getUser()->getUserIdentifier(),
            'lockeddate' => date('Y-m-d H:i:s'),
        ], ['reqid' => $reqId]);

        return $this->redirectToRoute('app_project_requirement_detail', ['id' => $id, 'reqId' => $reqId]);
    }

    #[Route('/project/{id}/requirements/{reqId}/unlock', name: 'app_project_requirement_unlock', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function requirementUnlock(int $id, int $reqId, Connection $connection): Response
    {
        $requirement = $connection->fetchAssociative('SELECT * FROM requirement WHERE reqid = ? AND project_id = ?', [$reqId, $id]);

        if (!$requirement) {
            throw $this->createNotFoundException('Requirement not found');
        }

        // Only the user who locked it (or an admin) can unlock
        $currentUser = $this->getUser()->getUserIdentifier();
        if ($requirement['lockedby'] !== $currentUser && !$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Only the user who locked this requirement (or an admin) can unlock it.');
            return $this->redirectToRoute('app_project_requirement_detail', ['id' => $id, 'reqId' => $reqId]);
        }

        $connection->update('requirement', [
            'lockedby' => '',
            'lockeddate' => '',
        ], ['reqid' => $reqId]);

        return $this->redirectToRoute('app_project_requirement_detail', ['id' => $id, 'reqId' => $reqId]);
    }

    #[Route('/project/{id}/requirements/{reqId}/test-assoc', name: 'app_project_requirement_test_assoc', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function requirementTestAssoc(int $id, int $reqId, Request $request, Connection $connection): Response
    {
        $project = $connection->fetchAssociative('SELECT * FROM project WHERE project_id = ?', [$id]);
        $requirement = $connection->fetchAssociative('SELECT * FROM requirement WHERE reqid = ? AND project_id = ?', [$reqId, $id]);

        if (!$project || !$requirement) {
            throw $this->createNotFoundException('Requirement not found');
        }

        if ($request->isMethod('POST')) {
            $testIds = $request->request->all('test_ids');
            $percentages = $request->request->all('percent_covered');

            // Clear existing associations
            $connection->delete('testsuite_requirement_assoc', ['reqid' => $reqId]);

            // Insert new associations
            foreach ($testIds as $testId) {
                $pcCovered = isset($percentages[$testId]) ? (int)$percentages[$testId] : 0;
                $connection->insert('testsuite_requirement_assoc', [
                    'testid' => (int)$testId,
                    'reqid' => $reqId,
                    'percentcovered' => $pcCovered,
                ]);
            }

            return $this->redirectToRoute('app_project_requirement_detail', ['id' => $id, 'reqId' => $reqId]);
        }

        // Get all tests for this project
        $allTests = $connection->fetchAllAssociative(
            'SELECT testid, testsuitename FROM testsuite WHERE project_id = ? ORDER BY testsuitename', [$id]
        );

        // Get currently associated tests with % covered
        $currentAssocs = $connection->fetchAllAssociative(
            'SELECT testid, percentcovered FROM testsuite_requirement_assoc WHERE reqid = ?', [$reqId]
        );
        $assocMap = [];
        foreach ($currentAssocs as $a) {
            $assocMap[$a['testid']] = $a['percentcovered'];
        }

        return $this->render('project/requirement_test_assoc.html.twig', [
            'project' => $project,
            'requirement' => $requirement,
            'allTests' => $allTests,
            'assocMap' => $assocMap,
        ]);
    }

    #[Route('/project/{id}/requirements/{reqId}/discussions', name: 'app_project_requirement_discussions', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function requirementDiscussions(int $id, int $reqId, Request $request, Connection $connection): Response
    {
        $project = $connection->fetchAssociative('SELECT * FROM project WHERE project_id = ?', [$id]);
        $requirement = $connection->fetchAssociative('SELECT * FROM requirement WHERE reqid = ? AND project_id = ?', [$reqId, $id]);

        if (!$project || !$requirement) {
            throw $this->createNotFoundException('Requirement not found');
        }

        // Handle new discussion creation
        if ($request->isMethod('POST')) {
            $subject = $request->request->get('subject');
            $discussion = $request->request->get('discussion');
            $assignTo = $request->request->get('assign_to', '');

            $connection->insert('discussion', [
                'reqid' => $reqId,
                'discsubject' => $subject,
                'discussion' => $discussion,
                'status' => 'OPEN',
                'author' => $this->getUser()->getUserIdentifier(),
                'assignto' => $assignTo,
                'date' => date('Y-m-d H:i:s'),
            ]);

            return $this->redirectToRoute('app_project_requirement_discussions', ['id' => $id, 'reqId' => $reqId]);
        }

        $discussions = $connection->fetchAllAssociative(
            'SELECT * FROM discussion WHERE reqid = ? ORDER BY discussionid DESC', [$reqId]
        );

        // Get users for the assign dropdown
        $users = $this->getProjectUsers($id, $connection);

        return $this->render('project/requirement_discussions.html.twig', [
            'project' => $project,
            'requirement' => $requirement,
            'discussions' => $discussions,
            'users' => $users,
        ]);
    }

    #[Route('/project/{id}/requirements/{reqId}/discussions/{discId}', name: 'app_project_requirement_discussion_view', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function requirementDiscussionView(int $id, int $reqId, int $discId, Request $request, Connection $connection): Response
    {
        $project = $connection->fetchAssociative('SELECT * FROM project WHERE project_id = ?', [$id]);
        $requirement = $connection->fetchAssociative('SELECT * FROM requirement WHERE reqid = ? AND project_id = ?', [$reqId, $id]);
        $discussion = $connection->fetchAssociative('SELECT * FROM discussion WHERE discussionid = ? AND reqid = ?', [$discId, $reqId]);

        if (!$project || !$requirement || !$discussion) {
            throw $this->createNotFoundException('Discussion not found');
        }

        // Handle new post
        if ($request->isMethod('POST')) {
            $post = $request->request->get('post');

            $connection->insert('discussionpost', [
                'discussionid' => $discId,
                'post' => $post,
                'author' => $this->getUser()->getUserIdentifier(),
                'date' => date('Y-m-d H:i:s'),
            ]);

            return $this->redirectToRoute('app_project_requirement_discussion_view', [
                'id' => $id, 'reqId' => $reqId, 'discId' => $discId
            ]);
        }

        $posts = $connection->fetchAllAssociative(
            'SELECT * FROM discussionpost WHERE discussionid = ? ORDER BY postid ASC', [$discId]
        );

        return $this->render('project/requirement_discussion_view.html.twig', [
            'project' => $project,
            'requirement' => $requirement,
            'discussion' => $discussion,
            'posts' => $posts,
        ]);
    }

    #[Route('/project/{id}/requirements/{reqId}/discussions/{discId}/close', name: 'app_project_requirement_discussion_close', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function requirementDiscussionClose(int $id, int $reqId, int $discId, Connection $connection): Response
    {
        $connection->update('discussion', [
            'status' => 'CLOSED',
        ], ['discussionid' => $discId, 'reqid' => $reqId]);

        return $this->redirectToRoute('app_project_requirement_discussion_view', [
            'id' => $id, 'reqId' => $reqId, 'discId' => $discId
        ]);
    }

    #[Route('/project/{id}/requirements/{reqId}/release-assoc', name: 'app_project_requirement_release_assoc', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function requirementReleaseAssoc(int $id, int $reqId, Request $request, Connection $connection): Response
    {
        $requirement = $connection->fetchAssociative('
            SELECT rv.reqversionid FROM requirementversion rv 
            WHERE rv.reqid = ? AND rv.latest = \'Y\'
        ', [$reqId]);

        if (!$requirement) {
            throw $this->createNotFoundException('Requirement version not found');
        }

        $releaseId = (int)$request->request->get('release_id');
        $action = $request->request->get('action');

        if ($action === 'add' && $releaseId > 0) {
            // Check if already exists
            $existing = $connection->fetchOne(
                'SELECT requirementversion_release_associd FROM requirementversion_release_assoc WHERE requirementversionid = ? AND releaseid = ?',
                [$requirement['reqversionid'], $releaseId]
            );
            if (!$existing) {
                $connection->insert('requirementversion_release_assoc', [
                    'requirementversionid' => $requirement['reqversionid'],
                    'releaseid' => $releaseId,
                ]);
            }
        } elseif ($action === 'remove' && $releaseId > 0) {
            $connection->executeStatement(
                'DELETE FROM requirementversion_release_assoc WHERE requirementversionid = ? AND releaseid = ?',
                [$requirement['reqversionid'], $releaseId]
            );
        }

        return $this->redirectToRoute('app_project_requirement_detail', ['id' => $id, 'reqId' => $reqId]);
    }

    private function logBugHistory(Connection $connection, int $bugId, string $user, string $field, string $oldVal, string $newVal): void
    {
        if ($oldVal === $newVal) {
            return;
        }
        $connection->insert('bughistory', [
            'bugid' => $bugId,
            'datemodified' => date('Y-m-d H:i:s'),
            'username' => $user,
            '"field"' => $field,
            'oldvalue' => $oldVal,
            'newvalue' => $newVal,
        ]);
    }

    #[Route('/project/{id}/bugs/new', name: 'app_project_bug_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function bugNew(int $id, Request $request, Connection $connection): Response
    {
        $project = $connection->fetchAssociative('SELECT * FROM project WHERE project_id = ?', [$id]);

        if (!$project) {
            throw $this->createNotFoundException('Project not found');
        }

        $lookups = $this->getBugLookups($id, $connection);

        if ($request->isMethod('POST')) {
            $summary = $request->request->get('summary', '');
            $description = $request->request->get('description', '');
            $priority = $request->request->get('priority', 'Medium');
            $severity = $request->request->get('severity', 'Minor');
            $category = (int)$request->request->get('category', 0);
            $component = (int)$request->request->get('component', 0);
            $assignedTo = $request->request->get('assignedto', '');
            $assignedToDeveloper = $request->request->get('assignedtodeveloper', '');
            $foundInRelease = $request->request->get('foundinrelease', '');
            $assignToRelease = $request->request->get('assigntorelease', '');
            $discoveryPeriod = $request->request->get('discoveryperiod', '');
            $testId = $request->request->get('testid', 0);
            $reqId = $request->request->get('reqid', 0);
            $reporter = $this->getUser()->getUserIdentifier();
            $reportedDate = date('Y-m-d H:i:s');

            $connection->insert('bug', [
                'projectid' => $id,
                'summary' => $summary,
                'description' => $description,
                'priority' => $priority,
                'severity' => $severity,
                'category' => $category,
                'component' => $component,
                'status' => 'New',
                'reporter' => $reporter,
                'reporteddate' => $reportedDate,
                'assignedto' => $assignedTo,
                'assignedtodeveloper' => $assignedToDeveloper,
                'foundinrelease' => $foundInRelease,
                'assigntorelease' => $assignToRelease,
                'discoveryperiod' => $discoveryPeriod,
                'testid' => (int)$testId,
                'reqid' => (int)$reqId,
                'closed' => 'N',
            ]);

            $bugId = (int)$connection->fetchOne('SELECT bugid FROM bug WHERE projectid = ? ORDER BY bugid DESC LIMIT 1', [$id]);

            $this->logBugHistory($connection, $bugId, $reporter, 'New Defect', '', 'Defect Reported');

            return $this->redirectToRoute('app_project_bug_detail', ['id' => $id, 'bugId' => $bugId]);
        }

        return $this->render('project/bug_new.html.twig', [
            'project' => $project,
            'lookups' => $lookups,
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

        $lookups = $this->getBugLookups($id, $connection);

        if ($request->isMethod('POST')) {
            $summary = $request->request->get('summary', '');
            $description = $request->request->get('description', '');
            $priority = $request->request->get('priority', 'Medium');
            $severity = $request->request->get('severity', 'Minor');
            $category = (int)$request->request->get('category', 0);
            $component = (int)$request->request->get('component', 0);
            $status = $request->request->get('status', 'New');
            $assignedTo = $request->request->get('assignedto', '');
            $assignedToDeveloper = $request->request->get('assignedtodeveloper', '');
            $foundInRelease = $request->request->get('foundinrelease', '');
            $assignToRelease = $request->request->get('assigntorelease', '');
            $implementedInRelease = $request->request->get('implementedinrelease', '');
            $closedReasonCode = $request->request->get('closedreasoncode', '');
            $discoveryPeriod = $request->request->get('discoveryperiod', '');
            $testId = $request->request->get('testid', 0);
            $reqId = $request->request->get('reqid', 0);
            $closed = ($status === 'Closed' || $status === 'Resolved') ? 'Y' : 'N';
            $closedDate = ($closed === 'Y' && ($bug['closed'] ?? 'N') === 'N') ? date('Y-m-d H:i:s') : ($bug['closeddate'] ?? null);

            $currentUser = $this->getUser()->getUserIdentifier();

            $fields = [
                'summary' => [$bug['summary'] ?? '', $summary],
                'description' => [$bug['description'] ?? '', $description],
                'priority' => [$bug['priority'] ?? '', $priority],
                'severity' => [$bug['severity'] ?? '', $severity],
                'category' => [$bug['category'] ?? '', $category],
                'component' => [$bug['component'] ?? '', $component],
                'status' => [$bug['status'] ?? '', $status],
                'assignedto' => [$bug['assignedto'] ?? '', $assignedTo],
                'assignedtodeveloper' => [$bug['assignedtodeveloper'] ?? '', $assignedToDeveloper],
                'foundinrelease' => [$bug['foundinrelease'] ?? '', $foundInRelease],
                'assigntorelease' => [$bug['assigntorelease'] ?? '', $assignToRelease],
                'implementedinrelease' => [$bug['implementedinrelease'] ?? '', $implementedInRelease],
                'closedreasoncode' => [$bug['closedreasoncode'] ?? '', $closedReasonCode],
                'discoveryperiod' => [$bug['discoveryperiod'] ?? '', $discoveryPeriod],
            ];

            foreach ($fields as $fieldName => [$oldVal, $newVal]) {
                $this->logBugHistory($connection, $bugId, $currentUser, $fieldName, (string)$oldVal, (string)$newVal);
            }

            $connection->update('bug', [
                'summary' => $summary,
                'description' => $description,
                'priority' => $priority,
                'severity' => $severity,
                'category' => $category,
                'component' => $component,
                'status' => $status,
                'assignedto' => $assignedTo,
                'assignedtodeveloper' => $assignedToDeveloper,
                'foundinrelease' => $foundInRelease,
                'assigntorelease' => $assignToRelease,
                'implementedinrelease' => $implementedInRelease,
                'closedreasoncode' => $closedReasonCode,
                'discoveryperiod' => $discoveryPeriod,
                'testid' => (int)$testId,
                'reqid' => (int)$reqId,
                'closed' => $closed,
                'closeddate' => $closedDate,
            ], ['bugid' => $bugId]);

            return $this->redirectToRoute('app_project_bug_detail', ['id' => $id, 'bugId' => $bugId]);
        }

        return $this->render('project/bug_edit.html.twig', [
            'project' => $project,
            'bug' => $bug,
            'lookups' => $lookups,
        ]);
    }

    #[Route('/project/{id}/bugs/{bugId}', name: 'app_project_bug_detail', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function bugDetail(int $id, int $bugId, Connection $connection): Response
    {
        $project = $connection->fetchAssociative('SELECT * FROM project WHERE project_id = ?', [$id]);
        $sql = 'SELECT b.*, bc.categoryname AS category_name, bco.componentname AS component_name 
                FROM bug b 
                LEFT JOIN bugcategory bc ON b.category = bc.categoryid 
                LEFT JOIN bugcomponent bco ON b.component = bco.componentid 
                WHERE b.bugid = ? AND b.projectid = ?';
        $bug = $connection->fetchAssociative($sql, [$bugId, $id]);

        if (!$project || !$bug) {
            throw $this->createNotFoundException('Bug not found');
        }

        $lookups = $this->getBugLookups($id, $connection);

        $notes = $connection->fetchAllAssociative('SELECT * FROM bugnote WHERE bugid = ? ORDER BY datecreated DESC', [$bugId]);
        $files = $connection->fetchAllAssociative('SELECT * FROM bugfile WHERE bugid = ? ORDER BY uploadeddate DESC', [$bugId]);
        $history = $connection->fetchAllAssociative('SELECT * FROM bughistory WHERE bugid = ? ORDER BY datemodified DESC', [$bugId]);
        $relationships = $connection->fetchAllAssociative('
            SELECT ba.*, b.summary, b.status 
            FROM bugassoc ba 
            JOIN bug b ON (ba.secondaryid = b.bugid) 
            WHERE ba.primaryid = ?
        ', [$bugId]);

        return $this->render('project/bug_detail.html.twig', [
            'project' => $project,
            'bug' => $bug,
            'lookups' => $lookups,
            'notes' => $notes,
            'files' => $files,
            'history' => $history,
            'relationships' => $relationships,
        ]);
    }

    #[Route('/project/{id}/bugs/{bugId}/action', name: 'app_project_bug_action', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function bugAction(int $id, int $bugId, Request $request, Connection $connection): Response
    {
        $bug = $connection->fetchAssociative('SELECT * FROM bug WHERE bugid = ? AND projectid = ?', [$bugId, $id]);
        if (!$bug) {
            throw $this->createNotFoundException('Bug not found');
        }

        $action = $request->request->get('action_type', '');
        $currentUser = $this->getUser()->getUserIdentifier();

        if ($action === 'update_assign_to') {
            $newAssignee = $request->request->get('assignedto', '');
            $this->logBugHistory($connection, $bugId, $currentUser, 'assignedto', $bug['assignedto'] ?? '', $newAssignee);
            $connection->update('bug', ['assignedto' => $newAssignee], ['bugid' => $bugId]);
        } elseif ($action === 'update_assign_to_developer') {
            $newDev = $request->request->get('assignedtodeveloper', '');
            $this->logBugHistory($connection, $bugId, $currentUser, 'assignedtodeveloper', $bug['assignedtodeveloper'] ?? '', $newDev);
            $connection->update('bug', ['assignedtodeveloper' => $newDev], ['bugid' => $bugId]);
        } elseif ($action === 'update_status') {
            $newStatus = $request->request->get('status', '');
            $closed = ($newStatus === 'Closed' || $newStatus === 'Resolved') ? 'Y' : 'N';
            $closedDate = ($closed === 'Y' && ($bug['closed'] ?? 'N') === 'N') ? date('Y-m-d H:i:s') : ($bug['closeddate'] ?? null);
            $this->logBugHistory($connection, $bugId, $currentUser, 'status', $bug['status'] ?? '', $newStatus);
            $connection->update('bug', ['status' => $newStatus, 'closed' => $closed, 'closeddate' => $closedDate], ['bugid' => $bugId]);
        } elseif ($action === 'add_bugnote') {
            $noteDetail = $request->request->get('bugnotedetail', '');
            if (!empty(trim($noteDetail))) {
                $connection->insert('bugnote', [
                    'bugid' => $bugId,
                    'author' => $currentUser,
                    'datecreated' => date('Y-m-d H:i:s'),
                    'bugnotedetail' => $noteDetail,
                ]);
                $this->logBugHistory($connection, $bugId, $currentUser, 'Bug Note', '', 'Added Note');
            }
        } elseif ($action === 'add_relationship') {
            $secondaryId = (int)$request->request->get('secondaryid', 0);
            $relType = $request->request->get('relationshiptype', 'Related To');
            $checkBug = $connection->fetchAssociative('SELECT bugid FROM bug WHERE bugid = ? AND projectid = ?', [$secondaryId, $id]);
            if ($checkBug && $secondaryId !== $bugId) {
                $connection->insert('bugassoc', [
                    'primaryid' => $bugId,
                    'secondaryid' => $secondaryId,
                    'relationshiptype' => $relType,
                ]);
                $this->logBugHistory($connection, $bugId, $currentUser, 'Relationship', '', "Linked #$secondaryId ($relType)");
            }
        } elseif ($action === 'upload_file') {
            $uploadedFile = $request->files->get('bug_file');
            if ($uploadedFile && $uploadedFile->isValid()) {
                $shareDir = $this->getParameter('kernel.project_dir') . '/var/share/bugs';
                if (!is_dir($shareDir)) {
                    mkdir($shareDir, 0777, true);
                }
                $origName = $uploadedFile->getClientOriginalName();
                $storedFilename = uniqid() . '_' . $origName;
                $uploadedFile->move($shareDir, $storedFilename);

                $connection->insert('bugfile', [
                    'bugid' => $bugId,
                    'uploadeddate' => date('Y-m-d H:i:s'),
                    'uploadedby' => $currentUser,
                    'displayname' => $origName,
                    'bugfilename' => $storedFilename,
                ]);
                $this->logBugHistory($connection, $bugId, $currentUser, 'File Attached', '', $origName);
            }
        } elseif ($action === 'delete_bugnote') {
            $noteId = (int)$request->request->get('bugnoteid', 0);
            $connection->delete('bugnote', ['bugnoteid' => $noteId, 'bugid' => $bugId]);
            $this->logBugHistory($connection, $bugId, $currentUser, 'Bug Note', 'Deleted Note', '');
        } elseif ($action === 'delete_relationship') {
            $assocId = (int)$request->request->get('bugassocid', 0);
            $connection->delete('bugassoc', ['bugassocid' => $assocId, 'primaryid' => $bugId]);
            $this->logBugHistory($connection, $bugId, $currentUser, 'Relationship', 'Removed Link', '');
        }

        return $this->redirectToRoute('app_project_bug_detail', ['id' => $id, 'bugId' => $bugId]);
    }

    #[Route('/project/{id}/bugs/file/{fileId}', name: 'app_project_bug_file_download', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function downloadBugFile(int $id, int $fileId, Connection $connection): Response
    {
        $file = $connection->fetchAssociative('
            SELECT bf.* 
            FROM bugfile bf 
            JOIN bug b ON bf.bugid = b.bugid 
            WHERE bf.bugfileid = ? AND b.projectid = ?
        ', [$fileId, $id]);

        if (!$file) {
            throw $this->createNotFoundException('File not found');
        }

        $shareDir = $this->getParameter('kernel.project_dir') . '/var/share/bugs';
        $filePath = $shareDir . '/' . $file['bugfilename'];

        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('Physical file not found on disk');
        }

        return $this->file($filePath, $file['displayname']);
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
        $bug = $connection->fetchAssociative('SELECT * FROM bug WHERE bugid = ? AND projectid = ?', [$bugId, $id]);
        if ($bug) {
            $currentUser = $this->getUser()->getUserIdentifier();
            $this->logBugHistory($connection, $bugId, $currentUser, 'status', $bug['status'] ?? '', 'Resolved');
            $connection->update('bug', [
                'status' => 'Resolved',
                'closed' => 'Y',
                'closeddate' => date('Y-m-d H:i:s'),
            ], ['bugid' => $bugId, 'projectid' => $id]);
        }

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

    #[Route('/project/{id}/settings/requirements', name: 'app_project_settings_requirements', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function projectSettings(int $id, Request $request, Connection $connection): Response
    {
        $project = $connection->fetchAssociative('SELECT * FROM project WHERE project_id = ?', [$id]);

        if (!$project) {
            throw $this->createNotFoundException('Project not found');
        }

        if ($request->isMethod('POST')) {
            $action = $request->request->get('action');

            if ($action === 'add_doc_type') {
                $name = trim($request->request->get('doc_type_name'));
                if ($name) {
                    $connection->insert('requirementdocumenttype', [
                        'projectid' => $id,
                        'reqdoctypename' => $name,
                        'rootdocument' => 'N',
                    ]);
                }
            } elseif ($action === 'remove_doc_type') {
                $typeId = (int)$request->request->get('id');
                $connection->delete('requirementdocumenttype', ['reqdoctypeid' => $typeId, 'projectid' => $id]);
            } elseif ($action === 'add_area') {
                $name = trim($request->request->get('area_name'));
                if ($name) {
                    $connection->insert('requirementareacoverage', [
                        'projectid' => $id,
                        'areacoverage' => $name,
                    ]);
                }
            } elseif ($action === 'remove_area') {
                $areaId = (int)$request->request->get('id');
                $connection->delete('requirementareacoverage', ['reqareacoverageid' => $areaId, 'projectid' => $id]);
            } elseif ($action === 'add_func') {
                $name = trim($request->request->get('func_name'));
                if ($name) {
                    $connection->insert('requirementfunctionality', [
                        'projectid' => $id,
                        'functionalityname' => $name,
                    ]);
                }
            } elseif ($action === 'remove_func') {
                $funcId = (int)$request->request->get('id');
                $connection->delete('requirementfunctionality', ['functionalityid' => $funcId, 'projectid' => $id]);
            }

            return $this->redirectToRoute('app_project_settings_requirements', ['id' => $id]);
        }

        $lookups = $this->getRequirementLookups($id, $connection);

        return $this->render('project/settings_requirements.html.twig', [
            'project' => $project,
            'docTypes' => $lookups['docTypes'],
            'areas' => $lookups['areas'],
            'functionalities' => $lookups['functionalities'],
        ]);
    }

    #[Route('/project/{id}/settings/tests', name: 'app_project_settings_tests', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function testSettings(int $id, Request $request, Connection $connection): Response
    {
        $project = $connection->fetchAssociative('SELECT * FROM project WHERE project_id = ?', [$id]);

        if (!$project) {
            throw $this->createNotFoundException('Project not found');
        }

        if ($request->isMethod('POST')) {
            $action = $request->request->get('action');

            if ($action === 'add_area') {
                $name = trim($request->request->get('area_name'));
                if ($name) {
                    $connection->insert('testarea', [
                        'project_id' => $id,
                        'areatestedname' => $name,
                    ]);
                }
            } elseif ($action === 'remove_area') {
                $areaId = (int)$request->request->get('id');
                $connection->delete('testarea', ['areatestedid' => $areaId, 'project_id' => $id]);
            } elseif ($action === 'add_type') {
                $name = trim($request->request->get('type_name'));
                if ($name) {
                    $connection->insert('testtype', [
                        'project_id' => $id,
                        'testtype' => $name,
                    ]);
                }
            } elseif ($action === 'remove_type') {
                $typeId = (int)$request->request->get('id');
                $connection->delete('testtype', ['testtypeid' => $typeId, 'project_id' => $id]);
            }

            return $this->redirectToRoute('app_project_settings_tests', ['id' => $id]);
        }

        $lookups = $this->getTestLookups($id, $connection);

        return $this->render('project/settings_tests.html.twig', [
            'project' => $project,
            'areas' => $lookups['areas'],
            'types' => $lookups['types'],
        ]);
    }
}
