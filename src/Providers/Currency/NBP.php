<?php


namespace App\Providers\Currency;

use App\Providers\CurrencyProviderInterface;


class NBP implements CurrencyProviderInterface
{
    private $url = "http://api.nbp.pl/api/exchangerates";
    private $tables = ['A', 'B', 'C'];

    public function request($method, $table_code_or_date) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$this->url/$method/$table_code_or_date?format=json");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);

        if(!$response) {
            die('NBP currency provider connection error.');
        }

        $data = json_decode($response, true);
        return (count($data) == 1 ? $data[0] : $data);
    }

    public function getCurrencies() {
        $date = null;
        $rates = [];

        foreach($this->tables as $table) {
            $array = $this->request('tables', $table);
            $date = \DateTime::createFromFormat('Y-m-d', $array['effectiveDate']);
            foreach($array['rates'] as $row) {
                $rates[] = $row;
            }
        }

        return ['date'=>$date, 'rates'=>$rates];
    }

    public function getCurrency(string $iso) {
        $currencies = $this->getCurrencies();

        foreach($currencies['rates'] as $row) {
            if($row['code'] == $iso) {
                return ['date' => $currencies['date'], 'rate' => $row];
            }
        }

        return false;
    }
}