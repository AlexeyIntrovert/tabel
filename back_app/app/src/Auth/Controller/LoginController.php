<?php

namespace App\Auth\Controller;

use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class LoginController extends AbstractController
{
    #[Route('/auth/login', name: 'app_auth_login', methods: ['POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder, JWTTokenManagerInterface $JWTManager, UserProviderInterface $userProvider): Response
    {
        $data = json_decode($request->getContent(), true);

        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';

        if (empty($username) || empty($password)) {
            return new JsonResponse(['error' => 'Invalid credentials'], 400);
        }

        $user = $entityManager->getRepository(User::class)->findOneBy(['name' => $username]);

        if (!$user || !$passwordEncoder->isPasswordValid($user, $password)) {
            return new JsonResponse(['error' => 'Invalid credentials'], 400);
        }

        // Generate JWT token
        $token = $JWTManager->create($user);

        $response = new JsonResponse([
            'message' => 'Login successful',
            'user' => [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'roles' => $user->getRoles(),
            ],
        ]);

        // Set the token in the response headers
        $response->headers->set('Authorization', 'Bearer ' . $token);

        return $response;
    }
}
