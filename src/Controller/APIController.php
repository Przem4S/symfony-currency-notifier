<?php


namespace App\Controller;


use App\Entity\BaseEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class APIController extends AbstractController
{
    /**
     * Array data from content input
     *
     * @var array|mixed
     */
    protected $data;

    private $validator;

    /**
     * Simply parse input data
     *
     * @param RequestStack $requeststack
     */
    public function __construct(RequestStack $requeststack, ValidatorInterface $validator) {
        $this->data = json_decode($requeststack->getCurrentRequest()->getContent(), true) ?? [];
        $this->validator = $validator;
    }

    private function buildUserErrorResponse($violations) {
        $accessor = PropertyAccess::createPropertyAccessor();
        $errorMessages = [];

        foreach ($violations as $violation) {
            $accessor->setValue($errorMessages, "[".$violation->getPropertyPath()."]", $violation->getMessage());
        }

        return new JsonResponse(['success' => false, 'errors' => $errorMessages], 400);
    }

    public function validateInputContent(Assert\Collection $assertCollection) {
        $violations = $this->validator->validate($this->data, $assertCollection);

        if (count($violations) > 0) {
            return $this->buildUserErrorResponse($violations);
        }

        return true;
    }

    public function validateEntity($entity) {
        $violations = $this->validator->validate($entity);

        if (count($violations) > 0) {
            return $this->buildUserErrorResponse($violations);
        }

        return true;
    }

    public function getInputParameter(string $name) {
        return (isset($this->data[$name]) ? $this->data[$name] : null);
    }


}