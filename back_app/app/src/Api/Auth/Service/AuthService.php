<?php

namespace App\Api\Auth\Service;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Psr\Log\LoggerInterface;

class AuthService
{
    private $tokenStorage;
    private $security;
    private $jwtManager;
    private $logger;

    public function __construct(
        TokenStorageInterface $tokenStorage, 
        Security $security,
        JWTTokenManagerInterface $jwtManager,
        LoggerInterface $logger
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->security = $security;
        $this->jwtManager = $jwtManager;
        $this->logger = $logger;
    }

    public function authCheck(): bool
    {
        $this->logger->info('Checking authentication in auth service ');
        $token = $this->tokenStorage->getToken();
        
        $this->logger->info('Checking authentication', [
            'hasToken' => $token !== null,
            'tokenClass' => $token ? get_class($token) : 'null'
        ]);

        if (null === $token) {
            return false;
        }

        $user = $token->getUser();
        
        $this->logger->info('Checking user', [
            'hasUser' => $user !== null,
            'userClass' => $user ? get_class($user) : 'null'
        ]);

        if (!$user || $user === 'anon.') {
            return false;
        }

        return true;
    }
}