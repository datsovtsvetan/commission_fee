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

    function getWithdrawPercent(): float
    {
        return (self::WITHDRAW_PERCENT_TAX / 100);
    }
}