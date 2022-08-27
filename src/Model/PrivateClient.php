<?php

namespace App\Model;

class PrivateClient extends BaseClient
{
    const WITHDRAW_PERCENT_TAX = 0.3;
    const FREE_LIMIT_WITHDRAW_AMOUNT = 1000.00;
    const FREE_LIMIT_CURRENCY = 'EUR';
    const FREE_LIMIT_COUNT = 3;

    public function __construct(int $id)
    {
        parent::__construct($id);
    }

    function getWithdrawPercent(): float
    {
        return (self::WITHDRAW_PERCENT_TAX / 100);
    }

//    public function calculateWithdrawCommissionFee(\DateTimeImmutable $dateTime, float $amountInEuro):float|int
//    {
//        // TODO: Implement withdraw() method.
//
//        return 999.99;
//    }

}