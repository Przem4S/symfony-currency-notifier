<?php

namespace App\Controller\API;

use App\Controller\APIController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use FOS\UserBundle\Model\UserManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends APIController
{
    /**
     * @Route("/api/auth/token")
     */
    public function login(UserManagerInterface $userManager, JWTTokenManagerInterface $JWTManager): Response
    {
        $constraints = new Assert\Collection([
            'username' => new Assert\NotBlank,
            'password' => new Assert\notBlank,
        ]);

        $validate = $this->validateInputContent($constraints);

        if($validate instanceof Response) {
            return $validate;
        }

        return $this->redirectToRoute('api_auth_login', [
            'username' => 'api',
            'password' => getenv('API_PASSWORD')
        ], 307);
    }
}
