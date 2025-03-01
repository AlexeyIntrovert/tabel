<?php

namespace App\Api\Auth\Controller;

use App\Api\Auth\Service\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    #[Route('/', name: 'app_main')]
    public function index(): Response
    {
        error_log('AuthController::index called');
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
        error_log('AuthController::auth_check called');
        $isAuthenticated = $this->authService->authCheck();
        error_log('AuthController::auth_check isAuthenticated: ' . $isAuthenticated);
        return new JsonResponse(['authenticated' => $isAuthenticated]);
    }

    #[Route('/auth', name: 'app_need_auth', methods: ['GET'])]
    public function authNeedAuth(): Response
    {
        error_log('AuthController::authNeedAuth called');
        $response = new Response();
        $response->setContent(json_encode([
            'data' => 'authNeedAuth',
        ]));
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode(Response::HTTP_UNAUTHORIZED); // Set the status code to 401
        return $response;
    }
}