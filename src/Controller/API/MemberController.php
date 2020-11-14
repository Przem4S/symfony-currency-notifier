<?php

namespace App\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class MemberController extends AbstractController
{
    /**
     * @Route("/api/member/register", name="api_member_register")
     */
    public function register(): JsonResponse
    {
        return new JsonResponse(['success' => true]);
    }
}
