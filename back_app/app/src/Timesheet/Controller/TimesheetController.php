<?php

namespace App\Timesheet\Controller;

use App\Timesheet\Entity\Timesheet;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/timesheet')]
class TimesheetController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function getTimesheet(EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();
        $timesheet = $entityManager->getRepository(Timesheet::class)
            ->findBy(['userId' => $user->getId()]);

        return new JsonResponse(array_map(fn($entry) => [
            'id' => $entry->getId(),
            'date' => $entry->getDate()->format('Y-m-d'),
            'hours' => $entry->getHours(),
            'projectId' => $entry->getProjectId(),
            'groupId' => $entry->getGroupId()
        ], $timesheet));
    }

    #[Route('', methods: ['POST'])]
    public function addEntry(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->getUser();

        $entry = new Timesheet();
        $entry->setUserId($user->getId())
            ->setDate(new \DateTime($data['date']))
            ->setHours($data['hours'])
            ->setProjectId($data['projectId'])
            ->setGroupId($data['groupId']);

        $entityManager->persist($entry);
        $entityManager->flush();

        return new JsonResponse(['id' => $entry->getId()], Response::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function updateEntry(int $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->getUser();

        $entry = $entityManager->getRepository(Timesheet::class)->find($id);
        
        if (!$entry || $entry->getUserId() !== $user->getId()) {
            return new JsonResponse(['error' => 'Entry not found'], Response::HTTP_NOT_FOUND);
        }

        if (isset($data['date'])) {
            $entry->setDate(new \DateTime($data['date']));
        }
        if (isset($data['hours'])) {
            $entry->setHours($data['hours']);
        }
        if (isset($data['projectId'])) {
            $entry->setProjectId($data['projectId']);
        }
        if (isset($data['groupId'])) {
            $entry->setGroupId($data['groupId']);
        }

        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function deleteEntry(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();
        $entry = $entityManager->getRepository(Timesheet::class)->find($id);

        if (!$entry || $entry->getUserId() !== $user->getId()) {
            return new JsonResponse(['error' => 'Entry not found'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($entry);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}