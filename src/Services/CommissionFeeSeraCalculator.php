<?php

namespace App\Services;

use App\Interfaces\CurrencyConverterInterface;
use App\Interfaces\CommissionFeeCalculatorInterface;
use App\Model\BusinessClient;
use App\Model\PrivateClient;

class CommissionFeeSeraCalculator implements CommissionFeeCalculatorInterface
{

    private CurrencyConverterInterface $converter;

    public function __construct(CurrencyConverterInterface $converter)
    {
        $this->converter = $converter;
    }

    private function roundUp(float $value, int $precision = 2): float
    {
        $pow = \pow(10, $precision);
        return (\ceil($pow * $value) + \ceil($pow * $value - \ceil($pow * $value))) / $pow;
    }


    public function calculateWithdrawCommissionFeePrivateClient(PrivateClient $client, \DateTimeImmutable $date, float $amount, string $currency): float|int
    {
//        $converted = false;
//        if($currency != $client::CURRENCY){
//            $amount = $this->converter->convert($amount, $currency, $client::CURRENCY);
//            $converted = true;
//        }

        $beforeAmount = $client->getWithdrawnAmountByWeek($date);
        $isUnderFreeLimitAmount = $beforeAmount < $client::FREE_LIMIT_WITHDRAW_AMOUNT;

        $client->withdraw($date, $amount, $currency, $this->converter);

        if($client->getWithdrawnAmountByWeek($date) <= $client::FREE_LIMIT_WITHDRAW_AMOUNT
            && $client->getWithdrawnCountByWeek($date) <= $client::FREE_LIMIT_COUNT){
            return 0.0;
        }

        $withdrawnAmountToDefaultCurrency = $this->converter->convertToDefaultCurrency($amount, $currency);

        if($isUnderFreeLimitAmount){
           $amountExceedingFreeLimit = $beforeAmount + $withdrawnAmountToDefaultCurrency - $client::FREE_LIMIT_WITHDRAW_AMOUNT;

           $finalTaxToOriginalCurrency = $this->converter->convert($client::FREE_LIMIT_CURRENCY, $currency);

           return $finalTaxToOriginalCurrency * ($client::WITHDRAW_PERCENT_TAX / 100);
        }

        return $amount * ($client::WITHDRAW_PERCENT_TAX / 100);



        //$commissionFee = ($client->getWithdrawnAmountByWeek($date) )

    }

    public function calculateWithdrawCommissionFeeBusinessClient(BusinessClient $client, \DateTimeImmutable $date, float $amount, string $currency): float|int
    {
        // TODO: Implement calculateWithdrawCommissionFeeBusinessClient() method.
        return 999.99;
    }
}