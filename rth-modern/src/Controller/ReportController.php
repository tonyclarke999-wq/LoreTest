<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\DBAL\Connection;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ReportController extends AbstractController
{
    /**
     * Helper to fetch filter data (Releases, Builds, TestSets) for the project.
     */
    private function getFilters(int $projectId, Connection $connection): array
    {
        $releases = $connection->fetchAllAssociative('SELECT releaseid, releasename FROM release_tbl WHERE project_id = ? ORDER BY releasename', [$projectId]);
        
        // Flatten builds and test sets for the dropdowns (in a real complex app we might use ajax, but simple is fine here)
        $builds = $connection->fetchAllAssociative('
            SELECT b.buildid, b.buildname, b.releaseid 
            FROM build b 
            JOIN release_tbl r ON b.releaseid = r.releaseid 
            WHERE r.project_id = ? ORDER BY b.buildname
        ', [$projectId]);
        
        $testsets = $connection->fetchAllAssociative('
            SELECT ts.testsetid, ts.testsetname, ts.buildid 
            FROM testset ts
            JOIN build b ON ts.buildid = b.buildid
            JOIN release_tbl r ON b.releaseid = r.releaseid
            WHERE r.project_id = ? ORDER BY ts.testsetname
        ', [$projectId]);

        return [
            'releases' => $releases,
            'builds' => $builds,
            'testsets' => $testsets
        ];
    }

    #[Route('/project/{id}/reports', name: 'app_project_reports')]
    #[IsGranted('ROLE_USER')]
    public function index(int $id, Connection $connection): Response
    {
        $project = $connection->fetchAssociative('SELECT * FROM project WHERE project_id = ?', [$id]);
        if (!$project) {
            throw $this->createNotFoundException('Project not found');
        }

        return $this->render('report/index.html.twig', [
            'project' => $project,
        ]);
    }

    #[Route('/project/{id}/reports/test-area', name: 'app_report_test_area')]
    #[IsGranted('ROLE_USER')]
    public function testArea(int $id, Request $request, Connection $connection): Response
    {
        $project = $connection->fetchAssociative('SELECT * FROM project WHERE project_id = ?', [$id]);
        $filters = $this->getFilters($id, $connection);
        
        $testsetId = $request->query->get('testset_id');
        $results = [];

        if ($testsetId) {
            $results = $connection->fetchAllAssociative('
                SELECT t.areatested, COUNT(t.testid) as total_tests,
                       SUM(CASE WHEN assoc.teststatus = \'Passed\' THEN 1 ELSE 0 END) as passed_tests,
                       SUM(CASE WHEN assoc.teststatus = \'Failed\' THEN 1 ELSE 0 END) as failed_tests
                FROM testsuite t
                JOIN testset_testsuite_assoc assoc ON t.testid = assoc.testid
                WHERE assoc.testsetid = ?
                GROUP BY t.areatested
                ORDER BY t.areatested
            ', [$testsetId]);
        }

        return $this->render('report/report_test_area.html.twig', [
            'project' => $project,
            'filters' => $filters,
            'results' => $results,
            'selected_testset' => $testsetId
        ]);
    }

    #[Route('/project/{id}/reports/build-status', name: 'app_report_build_status')]
    #[IsGranted('ROLE_USER')]
    public function buildStatus(int $id, Request $request, Connection $connection): Response
    {
        $project = $connection->fetchAssociative('SELECT * FROM project WHERE project_id = ?', [$id]);
        $filters = $this->getFilters($id, $connection);
        
        $buildId = $request->query->get('build_id');
        $results = [];

        if ($buildId) {
            $results = $connection->fetchAllAssociative('
                SELECT ts.testsetname, t.testsuitename, assoc.teststatus, assoc.assignedto, assoc.logtimestamp
                FROM testset_testsuite_assoc assoc
                JOIN testsuite t ON assoc.testid = t.testid
                JOIN testset ts ON assoc.testsetid = ts.testsetid
                WHERE ts.buildid = ?
                ORDER BY ts.testsetname, t.testsuitename
            ', [$buildId]);
        }

        return $this->render('report/report_build_status.html.twig', [
            'project' => $project,
            'filters' => $filters,
            'results' => $results,
            'selected_build' => $buildId
        ]);
    }

    #[Route('/project/{id}/reports/failed-verifications', name: 'app_report_failed_verifications')]
    #[IsGranted('ROLE_USER')]
    public function failedVerifications(int $id, Request $request, Connection $connection): Response
    {
        $project = $connection->fetchAssociative('SELECT * FROM project WHERE project_id = ?', [$id]);
        $filters = $this->getFilters($id, $connection);
        
        $testsetId = $request->query->get('testset_id');
        $results = [];

        if ($testsetId) {
            // Find all failed verify results for the latest run of tests in this test set
            $results = $connection->fetchAllAssociative('
                SELECT t.testsuitename, v.stepnumber, v.expectedresult, v.actualresult, v.logtimestamp
                FROM verifyresults v
                JOIN testsuiteresults tr ON v.ts_uniquerunid = tr.ts_uniquerunid
                JOIN testsuite t ON tr.testid = t.testid
                WHERE tr.testsetid = ? AND v.teststatus = \'Failed\'
                ORDER BY t.testsuitename, v.stepnumber
            ', [$testsetId]);
        }

        return $this->render('report/report_failed_verifications.html.twig', [
            'project' => $project,
            'filters' => $filters,
            'results' => $results,
            'selected_testset' => $testsetId
        ]);
    }

    #[Route('/project/{id}/reports/req-coverage', name: 'app_report_req_coverage')]
    #[IsGranted('ROLE_USER')]
    public function reqCoverage(int $id, Request $request, Connection $connection): Response
    {
        $project = $connection->fetchAssociative('SELECT * FROM project WHERE project_id = ?', [$id]);
        $filters = $this->getFilters($id, $connection);
        
        $testsetId = $request->query->get('testset_id');
        $results = [];

        if ($testsetId) {
            $results = $connection->fetchAllAssociative('
                SELECT r.reqname, r.reqid, t.testsuitename, assoc.teststatus
                FROM requirement r
                LEFT JOIN testsuite_requirement_assoc tra ON r.reqid = tra.reqid
                LEFT JOIN testsuite t ON tra.testid = t.testid
                LEFT JOIN testset_testsuite_assoc assoc ON t.testid = assoc.testid AND assoc.testsetid = ?
                WHERE r.project_id = ?
                ORDER BY r.reqname, t.testsuitename
            ', [$testsetId, $id]);
        }

        return $this->render('report/report_req_coverage.html.twig', [
            'project' => $project,
            'filters' => $filters,
            'results' => $results,
            'selected_testset' => $testsetId
        ]);
    }

    #[Route('/project/{id}/reports/signoff', name: 'app_report_test_signoff')]
    #[IsGranted('ROLE_USER')]
    public function signoff(int $id, Request $request, Connection $connection): Response
    {
        $project = $connection->fetchAssociative('SELECT * FROM project WHERE project_id = ?', [$id]);
        $filters = $this->getFilters($id, $connection);
        
        $testsetId = $request->query->get('testset_id');
        $result = null;

        if ($testsetId) {
            $result = $connection->fetchAssociative('
                SELECT testsetname, signoffdate, signoffby, signoffcomments, testsetstatus
                FROM testset
                WHERE testsetid = ?
            ', [$testsetId]);
        }

        return $this->render('report/report_signoff.html.twig', [
            'project' => $project,
            'filters' => $filters,
            'result' => $result,
            'selected_testset' => $testsetId
        ]);
    }

    #[Route('/project/{id}/reports/test-sets', name: 'app_report_test_sets')]
    #[IsGranted('ROLE_USER')]
    public function testSets(int $id, Connection $connection): Response
    {
        $project = $connection->fetchAssociative('SELECT * FROM project WHERE project_id = ?', [$id]);
        
        $results = $connection->fetchAllAssociative('
            SELECT ts.testsetname, ts.datecreated, ts.testsetstatus, b.buildname, r.releasename
            FROM testset ts
            JOIN build b ON ts.buildid = b.buildid
            JOIN release_tbl r ON b.releaseid = r.releaseid
            WHERE r.project_id = ?
            ORDER BY ts.datecreated DESC
        ', [$id]);

        return $this->render('report/report_test_sets.html.twig', [
            'project' => $project,
            'results' => $results
        ]);
    }

    // PHASE 2: CUSTOM REPORTING

    #[Route('/project/{id}/reports/custom', name: 'app_report_custom_build', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function customBuild(int $id, Connection $connection): Response
    {
        $project = $connection->fetchAssociative('SELECT * FROM project WHERE project_id = ?', [$id]);
        
        return $this->render('report/custom_build.html.twig', [
            'project' => $project
        ]);
    }

    #[Route('/project/{id}/reports/custom/view', name: 'app_report_custom_view', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function customView(int $id, Request $request, Connection $connection): Response
    {
        $project = $connection->fetchAssociative('SELECT * FROM project WHERE project_id = ?', [$id]);
        
        $entity = $request->request->get('entity'); // 'testsuite', 'bug', or 'requirement'
        $columns = $request->request->all('columns'); // array of column names
        $statusFilter = $request->request->get('status');

        if (!$entity || empty($columns)) {
            return $this->redirectToRoute('app_report_custom_build', ['id' => $id]);
        }

        // Validate entity to prevent SQL injection
        $allowedEntities = ['testsuite' => 'project_id', 'bug' => 'projectid', 'requirement' => 'project_id'];
        if (!array_key_exists($entity, $allowedEntities)) {
            throw $this->createAccessDeniedException('Invalid entity selected.');
        }

        // Validate columns
        $sanitizedColumns = [];
        foreach ($columns as $col) {
            // Only allow alphanumeric and underscores
            if (preg_match('/^[a-zA-Z0-9_]+$/', $col)) {
                $sanitizedColumns[] = $col;
            }
        }

        if (empty($sanitizedColumns)) {
            return $this->redirectToRoute('app_report_custom_build', ['id' => $id]);
        }

        $projectIdCol = $allowedEntities[$entity];
        $colString = implode(', ', $sanitizedColumns);
        
        $sql = "SELECT $colString FROM $entity WHERE $projectIdCol = ?";
        $params = [$id];

        if ($statusFilter && $statusFilter !== 'Any') {
            $sql .= " AND status = ?";
            $params[] = $statusFilter;
        }

        $sql .= " LIMIT 500"; // Prevent massive loads

        $results = $connection->fetchAllAssociative($sql, $params);

        return $this->render('report/custom_view.html.twig', [
            'project' => $project,
            'results' => $results,
            'entity' => $entity,
            'columns' => $sanitizedColumns
        ]);
    }
}
