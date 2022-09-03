<?php

namespace App\Interfaces;

use App\Model\BaseClient;
use App\Model\BusinessClient;
use App\Model\PrivateClient;

interface CommissionFeeCalculatorInterface
{
    public function calculateWithdrawCommissionFeePrivateClient(
        PrivateClient $client,
        \DateTimeImmutable $date,
        float $amount,
        string $currency):float|int;

    public function calculateWithdrawCommissionFeeBusinessClient(
        BusinessClient $client,
        \DateTimeImmutable $date,
        float $amount,
        string $currency):float|int;

    public function calculateDepositCommissionFee(
        BaseClient $client,
        string $amount,
        string $currency):float;
}