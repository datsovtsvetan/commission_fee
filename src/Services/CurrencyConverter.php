<?php

namespace App\Services;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CurrencyConverter
{
    const CURRENCIES_URL = 'https://developers.paysera.com/tasks/api/currency-exchange-rates';

    private array $currencies;
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
        $this->currencies = [];
    }

    /**
     * both args must be 3 letters abbreviations of real currencies
     */
    public function convert(float $amount, string $from, string $to = 'EUR'):float
    {
        $from = strtoupper($from);
        $to = strtoupper($to);
        try {
            $this->currencies = $this->fetchCurrenciesFromApi();
        } catch (ClientExceptionInterface |
        DecodingExceptionInterface |
        RedirectionExceptionInterface |
        ServerExceptionInterface |
        TransportExceptionInterface $e) {

        }

        $conversion_rate  = $this->currencies[$from] / $this->currencies[$to];

        return round ($amount / $conversion_rate, 2);

    }

    public function convertToEuro(float $amount, string $currency):float
    {
        return $this->convert( $amount, $currency, 'EUR');
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