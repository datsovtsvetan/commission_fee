<?php

namespace App\Services;

use App\Interfaces\CurrencyConverterInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CurrencyMyCustomConverter implements CurrencyConverterInterface
{
    private const CURRENCIES_URL = 'https://developers.paysera.com/tasks/api/currency-exchange-rates';
    private array $currencies;
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
        $this->currencies = [];
    }

    /**
     * both args must be 3 letters abbreviations of real currencies
     * fetching the most current rates every time the method is used, on purpose.
     */
    public function convert(float $amount, string $from, string $to = 'EUR'):float
    {
        $from = trim(strtoupper($from));
        $to = trim(strtoupper($to));
        try {
            $this->currencies = $this->fetchCurrenciesFromApi();
        } catch (ClientExceptionInterface |
        DecodingExceptionInterface |
        RedirectionExceptionInterface |
        ServerExceptionInterface |
        TransportExceptionInterface $e) {
            echo $e->getMessage();
        }

        $conversion_rate  = $this->currencies[$from] / $this->currencies[$to];

        return round ($amount / $conversion_rate, 2);
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     */
    private function fetchCurrenciesFromApi():array
    {
        $response = $this->client->request('GET', self::CURRENCIES_URL);

        return $response->toArray(true)['rates'];
    }
}