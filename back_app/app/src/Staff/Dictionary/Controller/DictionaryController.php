<?php

namespace App\Staff\Dictionary\Controller;

use App\Staff\User\Entity\Group;
use App\Staff\User\Entity\Position;
use App\Staff\User\Entity\ProductionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DictionaryController extends AbstractController
{
    #[Route('/api/dictionary/groups', methods: ['GET'])]
    public function getGroups(EntityManagerInterface $em): JsonResponse
    {
        $groups = $em->getRepository(Group::class)->findAll();
        return new JsonResponse(array_map(fn($group) => [
            'id' => $group->getId(),
            'name' => $group->getName(),
            'code' => $group->getCode()
        ], $groups));
    }

    #[Route('/api/dictionary/production-types', methods: ['GET'])]
    public function getProductionTypes(EntityManagerInterface $em): JsonResponse
    {
        $types = $em->getRepository(ProductionType::class)->findAll();
        return new JsonResponse(array_map(fn($type) => [
            'id' => $type->getId(),
            'name' => $type->getName()
        ], $types));
    }

    #[Route('/api/dictionary/positions', methods: ['GET'])]
    public function getPositions(EntityManagerInterface $em): JsonResponse
    {
        $positions = $em->getRepository(Position::class)->findAll();
        return new JsonResponse(array_map(fn($position) => [
            'id' => $position->getId(),
            'name' => $position->getName(),
            'grade' => $position->getGrade()
        ], $positions));
    }
}