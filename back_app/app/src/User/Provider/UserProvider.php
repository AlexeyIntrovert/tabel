<?php

namespace App\User\Provider;

use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface, PasswordUpgraderInterface
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

    public function loadUserByUsername(string $username): UserInterface
    {
        $this->logger->info('Attempting to load user ' . $$username . ' by username');
        return $this->loadUserByIdentifier($username);
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $this->logger->info('Attempting to load user by identifier', [
            'identifier' => $identifier,
            'method' => __METHOD__,
            'timestamp' => new \DateTime()
        ]);
        
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $identifier]);

        if (!$user) {
            $this->logger->warning('User not found', [
                'identifier' => $identifier,
                'method' => __METHOD__,
                'timestamp' => new \DateTime()
            ]);
            throw new UsernameNotFoundException(sprintf('User "%s" not found.', $identifier));
        }

        $this->logger->info('User found successfully', [
            'identifier' => $identifier,
            'user_id' => $user->getId(),
            'roles' => $user->getRoles(),
            'method' => __METHOD__,
            'timestamp' => new \DateTime()
        ]);

        return $user;
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        return User::class === $class || is_subclass_of($class, User::class);
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}