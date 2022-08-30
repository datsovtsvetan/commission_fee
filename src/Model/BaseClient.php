<?php

namespace App\Model;

abstract class BaseClient
{
    public const ACCOUNT_CURRENCY = "EUR";
    protected array $withdrawsPerWeek;
    private const DEPOSIT_PERCENT_TAX = 0.03;
    private const AMOUNT = 'amount';
    private const COUNT = 'count';
    private int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
        $this->withdrawsPerWeek = [];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function withdraw(\DateTimeImmutable $dateTime, float $amount):void
    {
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

    /**
     * This key is used as identifier of the week that the withdraw is performed
     * Weeks that share days from different adjacent years are also considered and in format like '2021-2022'
     * All other weeks within same year are in format like 'year:2022week:34'
     */
    private function getWeekKey(\DateTimeImmutable $date):string
    {
        // Check if date is in the last week of year and shares
        // same week with next year several days:
        $year = $date->format('Y');
        $lastDayOfYear = \DateTimeImmutable::createFromFormat("Y-m-d",
            "$year-12-31");
        $lastDayOfYearWeekNumber = $lastDayOfYear->format('W');
        $weekNumber = $date->format('W');

        $isOnLastWeek = ($weekNumber == $lastDayOfYearWeekNumber);

        if($isOnLastWeek){
            for ($i=0;$i<7;$i++){
                $nextDateWeekNumber = $date->add(new \DateInterval('P'.$i.'D'))
                    ->format('W');

                if($nextDateWeekNumber != $weekNumber){
                    $nextYear = (int) $year + 1;
                    //var_dump($year."-".$nextYear);
                    return $year."-".$nextYear;
                }
            }
        }

        // END check date is in last week of the year...

        // Check if the date is in first week in the year
        // and shares same week with last year several days:

        /**
         * to make sure the format returned from is valid (i.e $date->format('W') => 1 or 01)
         * and not brakes if format changes between php versions.
        */
        $firstWeekOfYear = \DateTimeImmutable::createFromFormat("Y-m-d",
            "$year-01-01")->format('W');

        $isOnFirstWeek = ($weekNumber == $firstWeekOfYear);

        if($isOnFirstWeek){
            for ($i=0;$i<7;$i++){
                $prevDateWeekNumber = $date->sub(new \DateInterval('P'.$i.'D'))
                    ->format('W');

                if($prevDateWeekNumber != $weekNumber){
                    $prevYear = (int) $year - 1;
                    //var_dump($prevYear."-".$year);
                    return $prevYear."-".$year;
                }
            }
        }
        // END check date is in first week of year and shares
        // same week with last year several days

        return "year:".$date->format('Y')."week:".$date->format('W');
    }

    abstract protected function getWithdrawPercent():float;
}