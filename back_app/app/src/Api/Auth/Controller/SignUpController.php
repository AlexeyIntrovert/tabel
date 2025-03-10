<?php

namespace App\Api\Auth\Controller;

use App\Staff\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class SignUpController extends AbstractController
{
    #[Route('/api/auth/signup', name: 'app_signup', methods: ['POST'])]
    public function index(
        Request $request, 
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $passwordHasher, 
        JWTTokenManagerInterface $JWTManager,
        ValidatorInterface $validator
    ): Response {
        $data = json_decode($request->getContent(), true);

        $constraints = new Assert\Collection([
            'username' => [new Assert\NotBlank(), new Assert\Length(['min' => 3])],
            'password' => [new Assert\NotBlank(), new Assert\Length(['min' => 6])],
            'email' => [new Assert\NotBlank(), new Assert\Email()],
        ]);

        $violations = $validator->validate($data, $constraints);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getMessage();
            }
            return new JsonResponse(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $user = new User();
        $user->setName($data['username']);
        $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
        $user->setEmail($data['email']);
        $user->setRoles(['ROLE_USER']); // Set default role

        $entityManager->persist($user);
        $entityManager->flush();

        $token = $JWTManager->create($user);

        return new JsonResponse(['token' => $token], Response::HTTP_CREATED);
    }
}