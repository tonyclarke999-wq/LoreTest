<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\DBAL\Connection;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    #[IsGranted('ROLE_USER')]
    public function index(Connection $connection): Response
    {
        $projects = $connection->fetchAllAssociative('SELECT project_id, project_name, description FROM project WHERE deleted = \'N\' ORDER BY project_name ASC');

        return $this->render('home/index.html.twig', [
            'projects' => $projects,
        ]);
    }

    #[Route('/project/new', name: 'app_project_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function newProject(Request $request, Connection $connection): Response
    {
        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $description = $request->request->get('description');

            $connection->insert('project', [
                'project_name' => $name,
                'description' => $description,
                'deleted' => 'N',
            ]);

            return $this->redirectToRoute('app_home');
        }

        return $this->render('home/project_new.html.twig');
    }

    #[Route('/project/{id}/edit', name: 'app_project_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function editProject(int $id, Request $request, Connection $connection): Response
    {
        $project = $connection->fetchAssociative('SELECT * FROM project WHERE project_id = ?', [$id]);

        if (!$project) {
            throw $this->createNotFoundException('Project not found');
        }

        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $description = $request->request->get('description');

            $connection->update('project', [
                'project_name' => $name,
                'description' => $description,
            ], ['project_id' => $id]);

            return $this->redirectToRoute('app_home');
        }

        return $this->render('home/project_edit.html.twig', [
            'project' => $project,
        ]);
    }

    #[Route('/project/{id}/delete', name: 'app_project_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteProject(int $id, Connection $connection): Response
    {
        // RTH often uses soft delete
        $connection->update('project', ['deleted' => 'Y'], ['project_id' => $id]);

        return $this->redirectToRoute('app_home');
    }
}
