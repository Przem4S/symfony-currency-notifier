<?php

namespace App\Controller\API;

use App\Controller\APIController;
use App\Entity\Member;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MemberController extends APIController
{
    /**
     * @Route("/api/member/register")
     */
    public function register(Request $request, ValidatorInterface $validator): JsonResponse
    {
        dd($this->data);
        exit;
        $member = new Member;
        $errors = $validator->validate($member);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return new Response($errorsString);
        }

        return new JsonResponse(['success' => true]);
    }

    /**
     * @Route("/api/member/subscribe")
     */
    public function subscribe(): JsonResponse
    {
        return new JsonResponse(['success' => true]);
    }

    /**
     * @Route("/api/member/unsubscribe/{token}/{currency}")
     */
    public function unsubscribe(): JsonResponse
    {
        return new JsonResponse(['success' => true]);
    }
}
