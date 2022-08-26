<?php

namespace App\Model;

class BusinessClient extends BaseClient
{
   // private array $withdrawsPerWeek;

    /**
     *  base class deposit percent can be overriden
     */
    //const DEPOSIT_PERCENT = 0.05;

    public function __construct(int $id)
    {
        //$this->withdrawsPerWeek = [];
        parent::__construct($id);
    }


    function calculateWithdrawCommissionFee(\DateTimeImmutable $dateTime, float $amountInEuro): float|int
    {
        // TODO: Implement withdraw() method.

        return 999.99;
    }
}