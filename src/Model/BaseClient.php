<?php

namespace App\Model;

use App\Interfaces\CurrencyConverterInterface;

abstract class BaseClient
{
    const DEPOSIT_PERCENT_TAX = 0.03;
    const AMOUNT = 'amount';
    const COUNT = 'count';

    private int $id;
    protected array $withdrawsPerWeek;

    public function __construct(int $id)
    {
        $this->id = $id;
        $this->withdrawsPerWeek = [];
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }



    public function getDepositPercent(): float|int
    {
        return (self::DEPOSIT_PERCENT_TAX / 100);
    }

    public function withdraw(\DateTimeImmutable $dateTime, float $amount, string $currency, CurrencyConverterInterface $converter):void
    {
//        if(isset($this->withdrawsHistoryPerWeek[$dateTime->format('Y')][$dateTime->format('W')])){
//            $this->withdrawsHistoryPerWeek[$dateTime->format('Y')][$dateTime->format('W')] += $amountInEuro;
//        } else {
//            $this->withdrawsHistoryPerWeek[$dateTime->format('Y')][$dateTime->format('W')] = $amountInEuro;
//        }

        $amount = $converter->convertToDefaultCurrency($amount, $currency);

        $key = $this->getYearAndWeekNumberKey($dateTime);

        if (isset($this->withdrawsPerWeek[$key])) {
            $this->withdrawsPerWeek[$key][self::AMOUNT] += $amount;
            $this->withdrawsPerWeek[$key][self::COUNT] += 1;

        } else {
            $this->withdrawsPerWeek[$key][self::AMOUNT] = $amount;
            $this->withdrawsPerWeek[$key][self::COUNT] = 1;
        }


    }

    public function getWithdrawnAmountByWeek(\DateTimeImmutable $date):float|int
    {
        $key = $this->getYearAndWeekNumberKey($date);
        if(isset($this->withdrawsPerWeek[$key])){
            return $this->withdrawsPerWeek[$key][self::AMOUNT];
        }

        return 0;

    }

    public function getWithdrawnCountByWeek(\DateTimeImmutable $date):int
    {
        $key = $this->getYearAndWeekNumberKey($date);
        if(isset($this->withdrawsPerWeek[$key])) {
            return $this->withdrawsPerWeek[$key][self::COUNT];
        }

        return 0;
    }

    abstract function getWithdrawPercent():float;


    /**
     * DELETE, TEST PURPOSE ONLY!
     */
    public function testOnlyGetHistoryWithdraws(): array
    {
        return $this->withdrawsPerWeek;
    }

    private function getYearAndWeekNumberKey(\DateTimeImmutable $dateTime):string
    {
        return "year:".$dateTime->format('Y')."week:".$dateTime->format('W');
    }

    //abstract function calculateWithdrawCommissionFee(\DateTimeImmutable $dateTime, float $amountInEuro): float|int;


}