<?php

namespace App\Tests\Staff\User\Repository;

use App\Staff\User\Entity\User;
use App\Staff\User\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class UserRepositoryTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager;
    private ?UserRepository $userRepository;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
        $this->userRepository = $this->entityManager->getRepository(User::class);
    }

    public function testUpgradePassword(): void
    {
        // Create a test user
        $user = new User();
        $user->setName('Test User');
        $user->setEmail('test@example.com');
        $user->setPassword('old_password');
        $user->setTabNum(123);
        $user->setGrKod(456);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Test password upgrade
        $newPassword = 'new_password_hash';
        $this->userRepository->upgradePassword($user, $newPassword);

        // Refresh entity from database
        $this->entityManager->refresh($user);

        $this->assertEquals($newPassword, $user->getPassword());
    }

    public function testUpgradePasswordWithInvalidUser(): void
    {
        $this->expectException(UnsupportedUserException::class);

        $invalidUser = new class implements \Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface {
            public function getPassword(): ?string
            {
                return 'password';
            }
        };

        $this->userRepository->upgradePassword($invalidUser, 'new_password');
    }

    public function testFindByEmail(): void
    {
        // Create a test user
        $email = 'find_test@example.com';
        $user = new User();
        $user->setName('Find Test User');
        $user->setEmail($email);
        $user->setPassword('password');
        $user->setTabNum(789);
        $user->setGrKod(101);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Test finding user by email
        $foundUser = $this->userRepository->findOneBy(['email' => $email]);

        $this->assertNotNull($foundUser);
        $this->assertEquals($email, $foundUser->getEmail());
    }

    protected function tearDown(): void
    {
        // Remove test data
        if ($this->entityManager) {
            $users = $this->userRepository->findBy([
                'email' => ['test@example.com', 'find_test@example.com']
            ]);
            
            foreach ($users as $user) {
                $this->entityManager->remove($user);
            }
            
            $this->entityManager->flush();
            $this->entityManager->close();
        }

        parent::tearDown();

        $this->entityManager = null;
        $this->userRepository = null;
    }
}