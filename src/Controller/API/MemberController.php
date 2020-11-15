<?php

namespace App\Controller\API;

use App\Controller\APIController;
use App\Entity\Member;
use App\Entity\Subscription;
use App\Repository\CurrencyRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;

class MemberController extends APIController
{
    /**
     * @Route("/api/member/register")
     */
    public function register(Request $request, ValidatorInterface $validator, CurrencyRepository $currencyRepository, EntityManagerInterface $em): JsonResponse
    {
        $member = (new Member)->setEmail($this->getInputParameter('email'))
            ->setFirstname($this->getInputParameter('firstname'))
            ->setLastname($this->getInputParameter('lastname'))
            ->setPhone($this->getInputParameter('phone'))
            ->setBirthdate(\DateTime::createFromFormat('Y-m-d', $this->getInputParameter('birthdate')));

        $validate = $this->validateEntity($member);

        if($validate instanceof JsonResponse) {
            return $validate;
        }

        $em->persist($member);
        $em->flush();

        $currencies = $this->getInputParameter('currencies') ?? [];
        $num_of_subscriptions = 0;

        foreach($currencies as $iso => $values) {
            $currency = $currencyRepository->findCurrencyByISO($iso);

            if(!$currency) {
                return new JsonResponse(['success' => false, 'errors' => ['currencies' => ["Currency $iso not found."]]]);
            }

            if($values['min'] >= $values['max']) {
                return new JsonResponse(['success' => false, 'errors' => ['currencies' => ["Currency maximal notify value have to be higher than minimal."]]]);
            }

            $subscription = (new Subscription())->setCurrency($currency)
                ->setMember($member)
                ->setMin($values['min'])
                ->setMax($values['max']);

            $em->persist($subscription);
            $em->flush();

            $num_of_subscriptions++;
        }


        return new JsonResponse(['success' => true, 'message' => "Member {$member->getEmail()} subscription saved - confirm email.".($num_of_subscriptions > 0 ? " Subscribed $num_of_subscriptions currencies." : "")]);
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
