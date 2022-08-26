<?php

namespace App\Model;

class PrivateClient extends BaseClient
{
    const FREE_LIMIT = 1000.00;
    const FREE_LIMIT_CURRENCY = 'EUR';

    public function __construct(int $id)
    {
        //$this->withdrawsPerWeek = [];
        parent::__construct($id);
    }

    public function calculateWithdrawCommissionFee(\DateTimeImmutable $dateTime, float $amountInEuro):float|int
    {
        // TODO: Implement withdraw() method.
        $this->withdraw($dateTime, $amountInEuro);
        return 999.99;
    }


}