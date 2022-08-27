<?php

namespace App\Services;

use App\Interfaces\CurrencyConverterInterface;
use App\Interfaces\CommissionFeeCalculatorInterface;
use App\Model\BaseClient;
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

        if($client->getWithdrawnAmountByWeek($date) < $client::FREE_LIMIT_WITHDRAW_AMOUNT
            && $client->getWithdrawnCountByWeek($date) <= $client::FREE_LIMIT_COUNT){
            return 0.0;
        }

        $isOperationInDefaultCurr = ($currency == $client::FREE_LIMIT_CURRENCY);

        if( ! $isOperationInDefaultCurr) {
            $amount = $this->converter->convertToDefaultCurrency($amount, $currency);
            // IN EUR (default)
            if($isUnderFreeLimitAmount) {
                $taxedAmount = ($beforeAmount + $amount) - $client::FREE_LIMIT_WITHDRAW_AMOUNT;
                $taxedAmountToOrgCurrency = $this->converter->convert($taxedAmount, $currency);

                return $taxedAmountToOrgCurrency * $client->getWithdrawPercent();
            }

            return $amount * $client->getWithdrawPercent();
        }

        if($isUnderFreeLimitAmount) {
            $taxedAmountToOrgCurrency = ($beforeAmount + $amount) - $client::FREE_LIMIT_WITHDRAW_AMOUNT;

            return $taxedAmountToOrgCurrency * $client->getWithdrawPercent();
        }

       return $amount * $client->getWithdrawPercent();
    }


    public function calculateWithdrawCommissionFeeBusinessClient(BusinessClient $client, \DateTimeImmutable $date, float $amount, string $currency): float|int
    {
        $client->withdraw($date, $amount, $currency, $this->converter);

        return $amount * $client->getWithdrawPercent();

    }

    public function calculateDepositCommissionFee(BaseClient $client, string $amount):float
    {
        return $amount * $client->getDepositPercent();
    }


}