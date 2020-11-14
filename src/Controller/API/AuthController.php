<?php

namespace App\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use FOS\UserBundle\Model\UserManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    /**
     * @Route("/api/auth/token")
     */
    public function login(UserManagerInterface $userManager, JWTTokenManagerInterface $JWTManager): Response
    {
        return $this->redirectToRoute('api_auth_login', [
            'username' => 'api',
            'password' => getenv('API_PASSWORD')
        ], 307);
    }

    /**
     * @Route("/api/member/register")
     */
    public function test() {
        return new JsonResponse(['dziala'=>true]);
    }
}
