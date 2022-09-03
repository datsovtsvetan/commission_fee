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
    private array $currencies = [];
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;

        try {
            $this->currencies = $this->fetchCurrenciesFromApi();
        } catch (ClientExceptionInterface |
        DecodingExceptionInterface |
        RedirectionExceptionInterface |
        ServerExceptionInterface |
        TransportExceptionInterface $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Both '$from' and '$to' args must be 3 letters abbreviations
     * of real, existing currencies.
     * The if check for empty is because in the phpUnit test the constructor
     * is disabled, so only the first time convert() is called, it fetches
     * the currencies rates from API.
     */
    public function convert(
        float $amount,
        string $from,
        string $to = 'EUR'):float
    {
        if(empty($this->currencies)){
            try {
                $this->currencies = $this->fetchCurrenciesFromApi();
            } catch (ClientExceptionInterface |
            DecodingExceptionInterface |
            RedirectionExceptionInterface |
            ServerExceptionInterface |
            TransportExceptionInterface $e) {
            echo $e->getMessage();
            }
        }

        $from = trim(strtoupper($from));
        $to = trim(strtoupper($to));

        $conversion_rate  = $this->currencies[$from] / $this->currencies[$to];

        return  $amount / $conversion_rate;
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     */
    public function fetchCurrenciesFromApi():array
    {
        $response = $this->client->request('GET', self::CURRENCIES_URL);

        return $response->toArray(true)['rates'];
    }
}