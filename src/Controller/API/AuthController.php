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
    public function login(UserManagerInterface $userManager, JWTTokenManagerInterface $JWTManager, ValidatorInterface $validator): Response
    {
        $constraints = new Assert\Collection([
            'username' => new Assert\NotBlank,
            'password' => new Assert\notBlank,
        ]);

        $violations = $validator->validate($this->data, $constraints);

        if (count($violations) > 0) {
            $accessor = PropertyAccess::createPropertyAccessor();
            $errorMessages = [];

            foreach ($violations as $violation) {
                $accessor->setValue($errorMessages, $violation->getPropertyPath(), $violation->getMessage());
            }

            return new JsonResponse(['success' => false, 'errors' => $errorMessages], 400);
        }

        return $this->redirectToRoute('api_auth_login', [
            'username' => 'api',
            'password' => getenv('API_PASSWORD')
        ], 307);
    }
}
