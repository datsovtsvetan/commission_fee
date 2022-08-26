<?php

namespace App\Model;

abstract class BaseClient
{

    const DEPOSIT_PERCENT = 0.03;

    private int $id;
    protected array $withdrawsHistoryPerWeek;

    public function __construct(int $id)
    {
        $this->id = $id;
        $this->withdrawsHistoryPerWeek = [];
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }



    protected function deposit(float $amount, string $currency): float|int
    {
        $commissionFee = ($amount * self::DEPOSIT_PERCENT) / 100;

        return 999.99;
    }

    protected function withdraw(\DateTimeImmutable $dateTime, float $amountInEuro):void
    {
        if(isset($this->withdrawsHistoryPerWeek[$dateTime->format('Y')][$dateTime->format('W')])){
            $this->withdrawsHistoryPerWeek[$dateTime->format('Y')][$dateTime->format('W')] += $amountInEuro;
        } else {
            $this->withdrawsHistoryPerWeek[$dateTime->format('Y')][$dateTime->format('W')] = $amountInEuro;
        }
    }

    public function testOnlyGetHistoryWithdraws(): array
    {
        return $this->withdrawsHistoryPerWeek;
    }

    abstract function calculateWithdrawCommissionFee(\DateTimeImmutable $dateTime, float $amountInEuro): float|int;

}