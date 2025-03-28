<?php

namespace App\Staff\User\Controller;

use App\Staff\Entity\User;
use App\Staff\User\Entity\ProductionType;
use App\Staff\User\Entity\Group;
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
            'email' => $user->getEmail(),
            'fullName' => $user->getFullName(),
            'tabNum' => $user->getTabNum(),
            'grade' => $user->getGrade(),
            'productionType' => $user->getProductionType()?->getId(),
            'position' => $user->getPosition()?->getId(),
            'group' => $user->getGroup()?->getId()
        ]);
    }

    #[Route('/api/profile', name: 'update_user_profile', methods: ['PUT'])]
    public function updateUserProfile(
        Request $request, 
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        if (isset($data['fullName'])) {
            $user->setFullName($data['fullName']);
        }
        if (isset($data['tabNum'])) {
            $user->setTabNum($data['tabNum']);
        }
        if (isset($data['grade'])) {
            $user->setGrade($data['grade']);
        }
        
        // Handle production type by ID
        if (isset($data['productionType'])) {
            $productionType = $entityManager
                ->getRepository(ProductionType::class)
                ->find($data['productionType']);
                
            if ($productionType) {
                $user->setProductionType($productionType);
            }
        }
        
        // Handle group by ID
        if (isset($data['group'])) {
            $group = $entityManager
                ->getRepository(Group::class)
                ->find($data['group']);
                
            if ($group) {
                $user->setGroup($group);
            }
        }

        try {
            $entityManager->flush();
            return new JsonResponse([
                'message' => 'Profile updated successfully'
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Failed to update profile'
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}