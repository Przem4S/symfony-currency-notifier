<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;

class APIController extends AbstractController
{
    protected $data;

    /**
     * Simply parse input data
     *
     * @param RequestStack $requeststack
     */
    public function __construct(RequestStack $requeststack) {
        $this->data = json_decode($requeststack->getCurrentRequest()->getContent(), true) ?? [];
    }
}