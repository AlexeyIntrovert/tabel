<?php

namespace App\Api\Project\Controller;

use App\Api\Project\Service\ProjectService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Security;
use Psr\Log\LoggerInterface;

#[Route('/api')]
class ProjectController extends AbstractController
{
    private ProjectService $projectService;
    private LoggerInterface $logger;

    public function __construct(
        ProjectService $projectService,
        LoggerInterface $logger
    ) {
        $this->projectService = $projectService;
        $this->logger = $logger;
    }

    /**
     * Create a new project
     * 
     * @OA\Post(
     *     path="/api/projects",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Project created"),
     *     @OA\Response(response=400, description="Invalid input")
     * )
     * @Security(name="Bearer")
     */
    #[Route('/projects', name: 'create_project', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['name'])) {
            return new JsonResponse(['error' => 'Name is required'], Response::HTTP_BAD_REQUEST);
        }

        $project = $this->projectService->createProject($data['name']);

        return new JsonResponse([
            'id' => $project->getId(),
            'uid' => $project->getUid(),
            'name' => $project->getName()
        ], Response::HTTP_CREATED);
    }

    /**
     * Update an existing project
     * 
     * @OA\Put(
     *     path="/api/projects/{uid}",
     *     @OA\Parameter(name="uid", in="path", required=true),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Project updated"),
     *     @OA\Response(response=404, description="Project not found")
     * )
     * @Security(name="Bearer")
     */
    #[Route('/projects/{uid}', name: 'update_project', methods: ['PUT'])]
    public function update(string $uid, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['name'])) {
            return new JsonResponse(['error' => 'Name is required'], Response::HTTP_BAD_REQUEST);
        }

        $project = $this->projectService->updateProject($uid, $data['name']);

        if (!$project) {
            return new JsonResponse(['error' => 'Project not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            'id' => $project->getId(),
            'uid' => $project->getUid(),
            'name' => $project->getName()
        ]);
    }

    /**
     * Delete a project (soft delete)
     * 
     * @OA\Delete(
     *     path="/api/projects/{uid}",
     *     @OA\Parameter(name="uid", in="path", required=true),
     *     @OA\Response(response=204, description="Project deleted"),
     *     @OA\Response(response=404, description="Project not found")
     * )
     * @Security(name="Bearer")
     */
    #[Route('/projects/{uid}', name: 'delete_project', methods: ['DELETE'])]
    public function delete(string $uid): JsonResponse
    {
        $success = $this->projectService->deleteProject($uid);

        if (!$success) {
            return new JsonResponse(['error' => 'Project not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Get all active projects
     * 
     * @OA\Get(
     *     path="/api/projects",
     *     @OA\Response(
     *         response=200,
     *         description="List of active projects",
     *         @OA\JsonContent(type="array", @OA\Items(type="object"))
     *     )
     * )
     * @Security(name="Bearer")
     */
    #[Route('/projects', name: 'get_projects', methods: ['GET'])]
    public function getProjects(): JsonResponse
    {
        $this->logger->info('Getting all active projects', [
            'user' => $this->getUser()->getUserIdentifier(),
            'timestamp' => new \DateTime(),
            'method' => __METHOD__
        ]);
        
        $projects = $this->projectService->getActiveProjects();
        
        $response = array_map(function($project) {
            return [
                'id' => $project->getId(),
                'uid' => $project->getUid(),
                'name' => $project->getName()
            ];
        }, $projects);

        $this->logger->info('Retrieved projects', [
            'count' => count($projects),
            'user' => $this->getUser()->getUserIdentifier(),
            'timestamp' => new \DateTime()
        ]);

        return new JsonResponse($response);
    }
}