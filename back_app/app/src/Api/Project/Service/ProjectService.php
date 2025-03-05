<?php

namespace App\Api\Project\Service;

use App\Api\Project\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;
use Psr\Log\LoggerInterface;

class ProjectService
{
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    ) {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    public function createProject(string $name): Project
    {
        $this->logger->info('Creating new project', [
            'name' => $name,
            'method' => __METHOD__
        ]);

        $project = new Project();
        $project->setUid(Uuid::v4()->toRfc4122())
                ->setName($name);

        $this->entityManager->persist($project);
        $this->entityManager->flush();

        $this->logger->info('Project created successfully', [
            'uid' => $project->getUid(),
            'name' => $project->getName()
        ]);

        return $project;
    }

    public function updateProject(string $uid, string $name): ?Project
    {
        $project = $this->entityManager->getRepository(Project::class)->findOneBy(['uid' => $uid]);
        
        if (!$project) {
            return null;
        }

        $project->setName($name);
        $this->entityManager->flush();

        return $project;
    }

    public function deleteProject(string $uid): bool
    {
        $project = $this->entityManager->getRepository(Project::class)->findOneBy(['uid' => $uid]);
        
        if (!$project) {
            return false;
        }

        $project->setDeleted(true);
        $this->entityManager->flush();

        return true;
    }

    public function getActiveProjects(): array
    {
        return $this->entityManager->getRepository(Project::class)->findBy(['deleted' => false]);
    }
}