<?php

namespace App\Model;

use App\Interfaces\CurrencyConverterInterface;

abstract class BaseClient
{
    const ACCOUNT_CURRENCY = "EUR";
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

    public function getId(): int
    {
        return $this->id;
    }

    public function withdraw(\DateTimeImmutable $dateTime, float $amount, string $currency, CurrencyConverterInterface $converter):void
    {
        if ($currency != self::ACCOUNT_CURRENCY){
            $amount = $converter->convertToDefaultCurrency($amount, $currency);
        }

        $key = $this->getWeekKey($dateTime);

        if (isset($this->withdrawsPerWeek[$key][self::AMOUNT])
            && isset($this->withdrawsPerWeek[$key][self::COUNT])) {

            $this->withdrawsPerWeek[$key][self::AMOUNT] += $amount;
            $this->withdrawsPerWeek[$key][self::COUNT] += 1;

        } else {
            $this->withdrawsPerWeek[$key][self::AMOUNT] = $amount;
            $this->withdrawsPerWeek[$key][self::COUNT] = 1;
        }
    }

    public function getWithdrawnAmountByWeek(\DateTimeImmutable $date):float|int
    {
        $key = $this->getWeekKey($date);
        if(isset($this->withdrawsPerWeek[$key][self::AMOUNT])){
            return $this->withdrawsPerWeek[$key][self::AMOUNT];
        }

        return 0;
    }

    public function getWithdrawnCountByWeek(\DateTimeImmutable $date):int
    {
        $key = $this->getWeekKey($date);
        if(isset($this->withdrawsPerWeek[$key][self::COUNT])) {
            return $this->withdrawsPerWeek[$key][self::COUNT];
        }

        return 0;
    }

    public function getDepositPercent(): float|int
    {
        return (self::DEPOSIT_PERCENT_TAX / 100);
    }

    abstract function getWithdrawPercent():float;

    /**
     * DELETE, TEST PURPOSE ONLY!
     */
    public function testOnlyGetHistoryWithdraws(): array
    {
        return $this->withdrawsPerWeek;
    }

    private function getWeekKey(\DateTimeImmutable $date):string
    {

        // check if is in the last week of year:
        $year = $date->format('Y');
        $lastDayOfYear = \DateTimeImmutable::createFromFormat("Y-m-d",
            "$year-12-31");
        $lastDayOfYearWeekNumber = $lastDayOfYear->format('W');
        $weekNumber = $date->format('W');

        $isOnLastWeek = ($weekNumber == $lastDayOfYearWeekNumber);

        if($isOnLastWeek){
            for ($i=0;$i<7;$i++){
                $nextDate = $date->add(new \DateInterval('P'.$i.'D'))
                    ->format('W');

                if($nextDate != $weekNumber){
                    $nextYear = (int) $year + 1;
                    //var_dump($year."-".$nextYear);
                    return $year."-".$nextYear;
                }
            }
        }

        //End check date is in last week of year

        // Check for first week in year
        /**
         * to make sure the format is valid (i.e $date->format('W') => 1 or 01)
         * and not brakes if format changes between php versions.
        */
        $firstWeekOfYear = \DateTimeImmutable::createFromFormat("Y-m-d",
            "$year-01-01")->format('W');

        $isOnFirstWeek = ($weekNumber == $firstWeekOfYear);

        if($isOnFirstWeek){
            for ($i=0;$i<7;$i++){
                $prevDate = $date->sub(new \DateInterval('P'.$i.'D'))
                    ->format('W');

                if($prevDate != $weekNumber){
                    $prevYear = (int) $year - 1;
                    //var_dump($prevYear."-".$year);
                    return $prevYear."-".$year;
                }
            }
        }

        //var_dump("year:".$date->format('Y')."week:".$date->format('W'));
        return "year:".$date->format('Y')."week:".$date->format('W');
    }
}