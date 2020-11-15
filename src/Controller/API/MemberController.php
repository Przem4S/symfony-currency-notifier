<?php

namespace App\Controller\API;

use App\Controller\APIController;
use App\Entity\Member;
use App\Entity\Subscription;
use App\Repository\CurrencyRepository;
use App\Repository\MemberRepository;
use App\Repository\SubscriptionRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class MemberController extends APIController
{
    /**
     * @Route("/api/member/register")
     */
    public function register(
        Request $request,
        ValidatorInterface $validator,
        CurrencyRepository $currencyRepository,
        EntityManagerInterface $em,
        MailerInterface $mailer): JsonResponse
    {
        $birthdate = \DateTime::createFromFormat('Y-m-d', $this->getInputParameter('birthdate'));

        // Fill and validate member
        $member = (new Member)->setEmail($this->getInputParameter('email'))
            ->setFirstname($this->getInputParameter('firstname'))
            ->setLastname($this->getInputParameter('lastname'))
            ->setPhone($this->getInputParameter('phone'))
            ->setBirthdate(($birthdate ? $birthdate : null));

        $validate = $this->validateEntity($member);

        if($validate instanceof JsonResponse) {
            return $validate;
        }

        // Fill and validate currencies (optional)
        $currencies = $this->getInputParameter('currencies') ?? [];
        $num_of_subscriptions = 0;
        $subscriptions = [];

        foreach($currencies as $iso => $values) {
            $iso = strtoupper($iso);
            $currency = $currencyRepository->findCurrencyByISO($iso);

            if(!$currency) {
                return new JsonResponse(['success' => false, 'errors' => ['currencies' => ["Currency $iso not found."]]], 400);
            }

            if($values['min'] >= $values['max']) {
                return new JsonResponse(['success' => false, 'errors' => ['currencies' => ["Currency maximal notify value have to be higher than minimal for currency $iso."]]], 400);
            }

            $subscription = (new Subscription())->setCurrency($currency)
                ->setMember($member)
                ->setMin($values['min'])
                ->setMax($values['max']);

            $subscriptions[] = $subscription;
        }

        // Save data
        $em->persist($member);
        $em->flush();

        foreach($subscriptions as $subscription) {
            $em->persist($subscription);
            $em->flush();
            $num_of_subscriptions++;
        }

        // Send confirmation
        $email = (new Email())
            ->from('info@symfony-currency-notifier.com')
            ->to($member->getEmail())
            ->subject('Successful registered to currency notifier!')
            ->html($this->renderView('member_register.html.twig', [
                'email' => $member->getEmail(),
                'link' => $request->getSchemeAndHttpHost().'/confirm/'.$member->getToken(),
                'subscriptions' => $subscriptions
            ]));

        $mailer->send($email);

        return new JsonResponse(['success' => true, 'message' => "Member {$member->getEmail()} for subscription saved - confirm email.".($num_of_subscriptions > 0 ? " Subscribed $num_of_subscriptions currencies." : "")]);
    }

    /**
     * @Route("/confirm/{token}")
     */
    public function confirm(
        Request $request,
        MemberRepository $memberRepository,
        SubscriptionRepository $subscriptionRepository,
        EntityManagerInterface $em,
        MailerInterface $mailer): JsonResponse
    {
        $token = $request->attributes->get('token');
        $member = $memberRepository->findByToken($token);
        $member->setActive(1);
        $em->persist($member);
        $em->flush();

        $subscriptions = $subscriptionRepository->findByMemberAndStatus($member, false);

        if(count($subscriptions) == 0) {
            return new JsonResponse(['success' => true, 'message' => "No subscriptions to activate"]);
        }

        foreach($subscriptions as $subscription) {
            $subscription->setActive(1);
            $em->persist($subscription);
            $em->flush();
        }

        // Send confirmation
        $email = (new Email())
            ->from('info@symfony-currency-notifier.com')
            ->to($member->getEmail())
            ->subject('Successful confirmed subscription!')
            ->html($this->renderView('member_confirmed.html.twig', [
                'email' => $member->getEmail(),
                'link' => $request->getSchemeAndHttpHost().'/unsubscribe/'.$member->getToken(),
                'subscriptions' => $subscriptions
            ]));

        $mailer->send($email);

        return new JsonResponse(['success' => true, 'message' => 'Member successful confirmed. You have now '.count($subscriptions).' ACTIVE subscriptions']);
    }

    /**
     * @Route("/unsubscribe/{token}")
     * @Route("/unsubscribe/{token}/{subscription_id}")
     */
    public function unsubscribe(
        Request $request,
        MemberRepository $memberRepository,
        SubscriptionRepository $subscriptionRepository,
        EntityManagerInterface $em,
        MailerInterface $mailer): JsonResponse
    {
        $token = $request->attributes->get('token');
        $subscription_id = $request->attributes->get('subscription_id');

        $member = $memberRepository->findByToken($token);
        $subscriptions = $subscriptionRepository->findByMemberAndStatus($member, true);

        if(count($subscriptions) == 0) {
            return new JsonResponse(['success' => true, 'message' => "No subscriptions to deactivate"]);
        }

        $unsubscribed = 0;
        foreach($subscriptions as $subscription) {
            if($subscription_id && $subscription->getId() != $subscription_id) {
                continue;
            }

            $subscription->setActive(0);
            $em->persist($subscription);
            $em->flush();
            $unsubscribed++;
        }

        // Send confirmation
        $email = (new Email())
            ->from('info@symfony-currency-notifier.com')
            ->to($member->getEmail())
            ->subject('Successful unsubscribed!')
            ->html($this->renderView('member_unsubscribed.html.twig', [
                'email' => $member->getEmail(),
                'link' => $request->getSchemeAndHttpHost().'/confirm/'.$member->getToken(),
                'subscriptions' => $subscriptions
            ]));

        $mailer->send($email);

        $subscriptions = $subscriptionRepository->findByMemberAndStatus($member, true);

        if(count($subscriptions) == 0) {
            $member->setActive(0);
            $em->persist($member);
            $em->flush();
        }

        return new JsonResponse(['success' => true, 'message' => ($unsubscribed > 0 ? 'Unsubscribe successful.' : 'No changes in subscription.').' Now you have '.count($subscriptions).' ACTIVE subscriptions.']);
    }
}
