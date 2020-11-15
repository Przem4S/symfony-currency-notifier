<?php


namespace App\Command;

use App\Providers\CurrencyProvider;
use App\Repository\SubscriptionRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Routing\RouterInterface;

class SendNotifications extends Command
{
    protected static $defaultName = 'currency:notify';
    private $provider;
    private $subscriptionRepository;
    private $mailer;
    private $template;
    private $router;

    public function __construct(string $name = null, CurrencyProvider $provider, SubscriptionRepository $subscriptionRepository, MailerInterface $mailer, ContainerInterface $container, RouterInterface $router)
    {
        $this->provider = $provider;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->mailer = $mailer;
        $this->template = $container->get('templating');
        $this->router = $router;
        parent::__construct($name);
    }

    protected function configure() {
        $this->setDescription('Send notifications to member about currency changes')
            ->setHelp('This command allows you send email notification to subscription members about currency chages');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $subscriptions = $this->subscriptionRepository->getSubscriptionsToNotify();
        $context = $this->router->getContext();
        foreach($subscriptions as $subscription) {
            // Send confirmation
            $email = (new Email())
                ->from('info@symfony-currency-notifier.com')
                ->to($subscription->getMember()->getEmail())
                ->subject('Notification about currency '.$subscription->getCurrency()->getIso().' change')
                ->html($this->template->render('currency_change_notification.html.twig', [
                    'subscription' => $subscription,
                    'link' => 'http://'.$context->getHost().'/unsubscribe/'.$subscription->getMember()->getToken()
                ]));

            $this->mailer->send($email);
        }
    }
}