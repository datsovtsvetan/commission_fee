<?php

namespace App\Model;

class PrivateClient extends BaseClient
{
    public const FREE_LIMIT_WITHDRAW_AMOUNT = 1000.00;
    public const FREE_LIMIT_COUNT = 3;
    private const WITHDRAW_PERCENT_TAX = 0.3;

    public function __construct(int $id)
    {
        parent::__construct($id);
    }

    public function getWithdrawPercent(): float
    {
        return (self::WITHDRAW_PERCENT_TAX / 100);
    }
}