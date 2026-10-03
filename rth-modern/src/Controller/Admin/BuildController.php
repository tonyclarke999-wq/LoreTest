<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\DBAL\Connection;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class BuildController extends AbstractController
{
    #[Route('/admin/releases', name: 'app_admin_releases')]
    public function index(Connection $connection): Response
    {
        $releases = $connection->fetchAllAssociative('
            SELECT r.*, p.project_name 
            FROM release_tbl r 
            JOIN project p ON r.project_id = p.project_id 
            WHERE r.archive = \'N\'
            ORDER BY r.releaseid DESC
        ');

        return $this->render('admin/build/index.html.twig', [
            'releases' => $releases,
        ]);
    }

    #[Route('/admin/releases/new', name: 'app_admin_release_new', methods: ['GET', 'POST'])]
    public function newRelease(Request $request, Connection $connection): Response
    {
        if ($request->isMethod('POST')) {
            $projectId = $request->request->get('project_id');
            $name = $request->request->get('name');
            $description = $request->request->get('description');

            $connection->insert('release_tbl', [
                'project_id' => $projectId,
                'releasename' => $name,
                'description' => $description,
                'archive' => 'N',
                'datecreated' => date('Y-m-d H:i:s'),
                'logtimestamp' => date('Y-m-d H:i:s'),
                'datereceived' => '',
                'platform' => '',
                'qasignoffdate' => '',
                'basignoffdate' => '',
                'qasignoffby' => '',
                'basignoffby' => '',
                'qasignoff' => '',
                'basignoff' => '',
            ]);

            return $this->redirectToRoute('app_admin_releases');
        }

        $projects = $connection->fetchAllAssociative('SELECT * FROM project WHERE deleted = \'N\'');
        return $this->render('admin/build/release_new.html.twig', [
            'projects' => $projects,
        ]);
    }

    #[Route('/admin/releases/{id}/edit', name: 'app_admin_release_edit', methods: ['GET', 'POST'])]
    public function editRelease(int $id, Request $request, Connection $connection): Response
    {
        $release = $connection->fetchAssociative('SELECT * FROM release_tbl WHERE releaseid = ?', [$id]);
        if (!$release) throw $this->createNotFoundException();

        if ($request->isMethod('POST')) {
            $connection->update('release_tbl', [
                'releasename' => $request->request->get('name'),
                'description' => $request->request->get('description'),
                'project_id' => $request->request->get('project_id'),
            ], ['releaseid' => $id]);

            return $this->redirectToRoute('app_admin_releases');
        }

        $projects = $connection->fetchAllAssociative('SELECT * FROM project WHERE deleted = \'N\'');
        return $this->render('admin/build/release_edit.html.twig', [
            'release' => $release,
            'projects' => $projects,
        ]);
    }

    #[Route('/admin/releases/{id}/builds', name: 'app_admin_builds')]
    public function builds(int $id, Connection $connection): Response
    {
        $release = $connection->fetchAssociative('SELECT * FROM release_tbl WHERE releaseid = ?', [$id]);
        $builds = $connection->fetchAllAssociative('SELECT * FROM build WHERE releaseid = ? ORDER BY buildid DESC', [$id]);

        return $this->render('admin/build/build_list.html.twig', [
            'release' => $release,
            'builds' => $builds,
        ]);
    }

    #[Route('/admin/releases/{releaseId}/builds/new', name: 'app_admin_build_new', methods: ['GET', 'POST'])]
    public function newBuild(int $releaseId, Request $request, Connection $connection): Response
    {
        if ($request->isMethod('POST')) {
            $connection->insert('build', [
                'releaseid' => $releaseId,
                'buildname' => $request->request->get('name'),
                'description' => $request->request->get('description'),
                'archive' => 'N',
                'datecreated' => date('Y-m-d H:i:s'),
                'logtimestamp' => date('Y-m-d H:i:s'),
                'datereceived' => '',
                'datefinished' => '',
            ]);

            return $this->redirectToRoute('app_admin_builds', ['id' => $releaseId]);
        }

        return $this->render('admin/build/build_new.html.twig', ['releaseId' => $releaseId]);
    }

    #[Route('/admin/builds/{id}/edit', name: 'app_admin_build_edit', methods: ['GET', 'POST'])]
    public function editBuild(int $id, Request $request, Connection $connection): Response
    {
        $build = $connection->fetchAssociative('SELECT * FROM build WHERE buildid = ?', [$id]);
        if (!$build) throw $this->createNotFoundException();

        if ($request->isMethod('POST')) {
            $connection->update('build', [
                'buildname' => $request->request->get('name'),
                'description' => $request->request->get('description'),
            ], ['buildid' => $id]);

            return $this->redirectToRoute('app_admin_builds', ['id' => $build['releaseid']]);
        }

        return $this->render('admin/build/build_edit.html.twig', ['build' => $build]);
    }

    #[Route('/admin/releases/{id}/delete', name: 'app_admin_release_delete', methods: ['POST'])]
    public function deleteRelease(int $id, Connection $connection): Response
    {
        $connection->update('release_tbl', ['archive' => 'Y'], ['releaseid' => $id]);
        return $this->redirectToRoute('app_admin_releases');
    }

    #[Route('/admin/builds/{id}/delete', name: 'app_admin_build_delete', methods: ['POST'])]
    public function deleteBuild(int $id, Connection $connection): Response
    {
        $build = $connection->fetchAssociative('SELECT releaseid FROM build WHERE buildid = ?', [$id]);
        $connection->update('build', ['archive' => 'Y'], ['buildid' => $id]);
        return $this->redirectToRoute('app_admin_builds', ['id' => $build['releaseid']]);
    }
}
