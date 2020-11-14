<?php


namespace App\Command;

use App\Providers\CurrencyProvider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCurrencies extends Command
{
    protected static $defaultName = 'currency:update';
    private $provider;

    public function __construct(string $name = null, CurrencyProvider $provider)
    {
        $this->provider = $provider;
        parent::__construct($name);
    }

    protected function configure() {
        $this->setDescription('Update all currencies')
            ->setHelp('This command allows you update all currencies from external provider like NBP');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $provider = $this->provider;

        if(!$provider->updateCurrencies()) {
            $output->writeln('Some goes wrong while updating currencies...');
        } else {
            $output->writeln('Currencies updated successful!');
        }
    }
}