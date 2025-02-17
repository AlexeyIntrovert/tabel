<?php

namespace App\Auth\Service;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;

class AuthService
{
    private $tokenStorage;
    private $security;

    public function __construct(TokenStorageInterface $tokenStorage, Security $security)
    {
        $this->tokenStorage = $tokenStorage;
        $this->security = $security;
    }

    public function authCheck(): bool
    {
        $token = $this->tokenStorage->getToken();

        if (null === $token) {
            return false;
        }

        $user = $token->getUser();

        if ($user === 'anon.') {
            return false;
        }

        return true;
    }
}