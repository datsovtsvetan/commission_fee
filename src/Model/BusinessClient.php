<?php

namespace App\Model;

class BusinessClient extends BaseClient
{
    private const WITHDRAW_PERCENT_TAX = 0.5;

    public function __construct(int $id)
    {
        parent::__construct($id);
    }

    public function getWithdrawPercent(): float
    {
        return (self::WITHDRAW_PERCENT_TAX / 100);
    }
}