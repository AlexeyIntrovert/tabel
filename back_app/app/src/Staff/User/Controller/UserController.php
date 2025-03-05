<?php

namespace App\Staff\User\Controller;

use App\Staff\Entity\User;
use App\Staff\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="create_user", methods={"POST"})
     * @OA\Post(
     *     path="/user",
     *     summary="Create a new user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="tab_num", type="integer"),
     *             @OA\Property(property="gr_kod", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="tab_num", type="integer"),
     *             @OA\Property(property="gr_kod", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     )
     * )
     */
    public function createUser(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['name']) || empty($data['tab_num']) || empty($data['gr_kod'])) {
            return new JsonResponse(['error' => 'Invalid input'], 400);
        }

        $user = new User();
        $user->setName($data['name']);
        $user->setTabNum($data['tab_num']);
        $user->setGrKod($data['gr_kod']);

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse([
            'id' => $user->getId(),
            'name' => $user->getName(),
            'tab_num' => $user->getTabNum(),
            'gr_kod' => $user->getGrKod()
        ], 201);
    }

    /**
     * @Route("/users", name="get_all_users", methods={"GET"})
     * @OA\Get(
     *     path="/users",
     *     summary="Get all users",
     *     @OA\Response(
     *         response=200,
     *         description="List of users",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="tab_num", type="integer"),
     *                 @OA\Property(property="gr_kod", type="integer")
     *             )
     *         )
     *     )
     * )
     */
    #[Route('/api/users', name: 'get_all_users', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'List of users',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'id', type: 'integer'),
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: "tab_num", type: "integer"),
                new OA\Property(property: "gr_kod", type: "integer")
            ]
        )
    )]

    public function getAllUsers(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();
        $data = [];

        foreach ($users as $user) {
            $data[] = [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'tab_num' => $user->getTabNum(),
                'gr_kod' => $user->getGrKod(),
            ];
        }

        return new JsonResponse($data, 200);
    }

    #[Route('/api/user/{id}', name: 'api_user', methods: ['GET'])]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        description: 'The ID of the user',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns the user data',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'id', type: 'integer'),
                new OA\Property(property: 'name', type: 'string')
            ]
        )
    )]
    public function getUserById(int $id, UserRepository $userRepository): JsonResponse
    {
        $user = $userRepository->find($id);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        $data = [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'tab_num' => $user->getTabNum(),
            'gr_kod' => $user->getGrKod(),
        ];

        return new JsonResponse($data, 200);
    }

    #[Route('/api/profile', name: 'get_user_profile', methods: ['GET'])]
    public function getUserProfile(): JsonResponse
    {
        $user = $this->getUser();
        
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            'fullName' => $user->getFullName(),
            'tabNum' => $user->getTabNum(),
            'grKod' => $user->getGrKod(),
            'productionType' => $user->getProductionType(),
            'position' => $user->getPosition(),
            'grade' => $user->getGrade()
        ]);
    }

    #[Route('/api/profile', name: 'update_user_profile', methods: ['PUT'])]
    public function updateUserProfile(Request $request): JsonResponse
    {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        if (isset($data['fullName'])) {
            $user->setFullName($data['fullName']);
        }
        if (isset($data['position'])) {
            $user->setPosition($data['position']);
        }
        if (isset($data['grade'])) {
            $user->setGrade($data['grade']);
        }
        if (isset($data['productionType'])) {
            $user->setProductionType($data['productionType']);
        }

        $this->entityManager->flush();

        return new JsonResponse([
            'message' => 'Profile updated successfully'
        ]);
    }
}