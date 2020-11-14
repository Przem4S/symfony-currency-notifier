<?php


namespace App\Providers;

use App\Entity\Currency;
use App\Providers\Currency\NBP;
use Doctrine\ORM\EntityManagerInterface;

class CurrencyProvider
{
    private $em;
    private $provider;

    function __construct($provider = NBP::class, EntityManagerInterface $entityManager) {
        $this->provider = new $provider;
        $this->em = $entityManager;
    }

    public function getCurrencies() {
        return $this->provider->getCurrencies();
    }

    /**
     * Save information about currency
     *
     * @param array $rate
     * @param \DateTime $updated_at
     */
    private function saveCurrencyRate(array $rate, \DateTime $updated_at) {
        $currency = $this->em->getRepository(Currency::class)->findCurrencyByISO($rate['code']);
        $current_value = (isset($rate['mid']) ? $rate['mid'] : $rate['ask']);

        if(!$currency) {
            $currency = (new Currency)->setCreatedAt(new \DateTime('now'));
        } elseif($currency->getCurrent() != $current_value) {
            $currency->setPrevious($currency->getCurrent());
        }

        $currency->setIso($rate['code'])
            ->setName($rate['currency'])
            ->setCurrent($current_value)
            ->setUpdatedAt($updated_at);

        $this->em->persist($currency);
        $this->em->flush();
    }

    /**
     * Loop all currencies and save new information
     *
     * @return bool
     */
    public function updateCurrencies() {
        $currencies = $this->provider->getCurrencies();

        foreach($currencies['rates'] as $rate) {
            $this->saveCurrencyRate($rate, $currencies['date']);
        }

        return true;
    }

    /**
     * Save information about one currency (by iso code)
     * @param string $iso
     * @return bool
     */
    public function updateCurrency(string $iso) {
        $iso = strtoupper($iso);
        $current = $this->provider->getCurrency($iso);
        $rate = $current['rate'];

        $this->saveCurrencyRate($rate, $rate['date']);

        return true;
    }
}