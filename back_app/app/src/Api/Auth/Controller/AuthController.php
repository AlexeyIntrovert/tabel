<?php

namespace App\Api\Auth\Controller;

use App\Api\Auth\Service\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

class AuthController extends AbstractController
{
    private LoggerInterface $logger;
    private AuthService $authService;

    public function __construct(AuthService $authService, LoggerInterface $logger)
    {
        $this->authService = $authService;
        $this->logger = $logger;
    }

    #[Route('/', name: 'app_main')]
    public function index(): Response
    {
        $this->logger->info('AuthController::index called');
        $response = new Response();
        $response->setContent(json_encode([
            'data' => 1234,
        ]));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    #[Route('/auth/check', name: 'auth_check', methods: ['GET'])]
    public function authCheck(): JsonResponse
    {
        $this->logger->info('AuthController::auth_check called');
        $isAuthenticated = $this->authService->authCheck();
        $this->logger->info('AuthController::auth_check isAuthenticated: ' . $isAuthenticated);
        return new JsonResponse(['authenticated' => $isAuthenticated]);
    }

    #[Route('/auth', name: 'app_need_auth', methods: ['GET'])]
    public function authNeedAuth(): Response
    {
        error_log('AuthController::authNeedAuth called');
        $this->logger->info('AuthController::authNeedAuth called');
        $response = new Response();
        $response->setContent(json_encode([
            'data' => 'authNeedAuth',
        ]));
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode(Response::HTTP_UNAUTHORIZED); // Set the status code to 401
        return $response;
    }

    #[Route('/api/auth/check', name: 'api_auth_check', methods: ['POST'])]
    public function check(): JsonResponse
    {
        error_log('Auth check requested in api_auth_check');
        $this->logger->info('Auth check requested in api_auth_check');

        $isAuthenticated = $this->authService->authCheck();
        $this->logger->info('AuthController::api_auth_check isAuthenticated: ' . $isAuthenticated);
        $user = $this->getUser();
        $this->logger->info('Auth check requested', [
            'user' => $user ? $user->getUserIdentifier() : 'anonymous',
            'timestamp' => new \DateTime()
        ]);

        if (!$user) {
            return new JsonResponse(
                ['message' => 'Authentication required'], 
                Response::HTTP_UNAUTHORIZED
            );
        }

        return new JsonResponse([
            'authenticated' => true,
            'user' => [
                'email' => $user->getEmail(),
                'roles' => $user->getRoles()
            ]
        ]);
    }
}