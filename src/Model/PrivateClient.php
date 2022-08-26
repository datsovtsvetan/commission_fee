<?php

namespace App\Model;

class PrivateClient extends BaseClient
{
    const FREE_LIMIT = 1000.00;
    const FREE_LIMIT_CURRENCY = 'EUR';

    public function withdraw(float $amount, string $currency):float|int
    {
        // TODO: Implement withdraw() method.
        return 999.99;
    }
}