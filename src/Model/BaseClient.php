<?php

namespace App\Model;

abstract class BaseClient
{

    const DEPOSIT_PERCENT = 0.03;

    private int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    protected function deposit(float $amount, string $currency): float|int
    {
        $commissionFee = ($amount * self::DEPOSIT_PERCENT) / 100;

        return 999.99;
    }

    abstract function withdraw(float $amount, string $currency): float|int;

}