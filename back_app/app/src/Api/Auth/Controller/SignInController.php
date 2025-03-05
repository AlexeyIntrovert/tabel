<?php

namespace App\Api\Auth\Controller;

use App\User\Entity\User;
use App\User\Provider\UserProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Psr\Log\LoggerInterface;

final class SignInController extends AbstractController
{
    #[Route('/api/signin', name: 'app_signin', methods: ['POST'])]
    public function index(
        Request $request, 
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $passwordHasher, 
        JWTTokenManagerInterface $JWTManager, 
        UserProvider $userProvider
    ): Response {
        $data = json_decode($request->getContent(), true);

        try {
            // Find the user by email
            $user = $userProvider->loadUserByIdentifier($data['email']);

            // Check if the password is valid
            if (!$passwordHasher->isPasswordValid($user, $data['password'])) {
                return new JsonResponse(['message' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
            }

            // Generate JWT token
            $token = $JWTManager->create($user);

            // Return response with JWT token
            return new JsonResponse([
                'token' => $token,
                'user' => [
                    'email' => $user->getEmail(),
                    'roles' => $user->getRoles()
                ]
            ]);

        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }
    }

    #[Route('/api/check', name: 'app_check', methods: ['GET'])]
    public function check(Request $request, LoggerInterface $logger): Response
    {
        $logger->info('POST request received');
        return new JsonResponse(['message' => 'check successful']);
    }
}
