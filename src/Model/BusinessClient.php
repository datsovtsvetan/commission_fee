<?php

namespace App\Model;

class BusinessClient extends BaseClient
{
    /**
     *  base class deposit percent can be overriden
     */
    const WITHDRAW_PERCENT_TAX = 0.5;

    public function __construct(int $id)
    {
        parent::__construct($id);
    }

//    public function deposit(float $amount): float|int
//    {
//        return parent::deposit($amount); // TODO: Change the autogenerated stub
//    }


//    function calculateWithdrawCommissionFee(\DateTimeImmutable $dateTime, float $amountInEuro): float|int
//    {
//        // TODO: Implement withdraw() method.
//
//        return 999.99;
//    }
    function getWithdrawPercent(): float
    {
        return (self::WITHDRAW_PERCENT_TAX / 100);
    }
}