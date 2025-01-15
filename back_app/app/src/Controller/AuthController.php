<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AuthController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(): Response
    {
        $response = new Response();
        $response->setContent(json_encode([
            'data' => 1234,
        ]));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}