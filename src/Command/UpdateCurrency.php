<?php


namespace App\Command;

use App\Providers\CurrencyProvider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class UpdateCurrency extends Command
{
    protected static $defaultName = 'currency:update-iso';
    private $provider;

    public function __construct(string $name = null, CurrencyProvider $provider)
    {
        $this->provider = $provider;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setDescription('Update specific currency by ISO code')
            ->setHelp('This command allows you update specific currency from external provider like NBP')
            ->addArgument('iso_code', InputArgument::REQUIRED, 'The currency ISO code is required (3 characters)');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $code = $input->getArgument('iso_code');

        if(!$this->provider->updateCurrency($code)) {
            $output->writeln("Some goes wrong while updating $code currency...");
        }

        $output->writeln("Currency $code updated successful!");
    }
}