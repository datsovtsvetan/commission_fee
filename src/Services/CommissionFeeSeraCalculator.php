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

    /**
     * this method rounds always up, i.e. 0.21 becomes 0.3
     */
    private function roundUp(float $value, int $precision = 2): float
    {
        $pow = \pow(10, $precision);
        return (\ceil($pow * $value)
                + \ceil($pow * $value - \ceil($pow * $value))) / $pow;
    }


    public function calculateWithdrawCommissionFeePrivateClient(PrivateClient $client, \DateTimeImmutable $date, float $amount, string $currency): float|int
    {
        $prevAmount = $client->getWithdrawnAmountByWeek($date);
        $isUnderFreeLimitAmount = $prevAmount
            < $client::FREE_LIMIT_WITHDRAW_AMOUNT;
        $isConversionNeeded = ($currency != $client::ACCOUNT_CURRENCY);
        $this->doWithdraw($client,
            $date,
            $amount,
            $currency,
            $isConversionNeeded);

        if($client->getWithdrawnAmountByWeek($date)
            <= $client::FREE_LIMIT_WITHDRAW_AMOUNT
            && $client->getWithdrawnCountByWeek($date)
            <= $client::FREE_LIMIT_COUNT){
            return 0.0;
        }

        if($isConversionNeeded) {

            if($isUnderFreeLimitAmount) {
                return $this->calculateDiff($client,
                    $prevAmount,
                    $amount,
                    $currency,
                    true );
            }

            $fullAmountInOrgCurrency = $this->converter
                ->convert($amount, $client::ACCOUNT_CURRENCY, $currency);
            $resultUnrounded = $fullAmountInOrgCurrency
                * $client->getWithdrawPercent();

            return $this->roundUp($resultUnrounded);
        }

        if($isUnderFreeLimitAmount) {
            return $this->calculateDiff($client,
                $prevAmount, $amount,
                $currency,
                false );
        }

        $resultUnrounded = $amount * $client->getWithdrawPercent();
        return $this->roundUp($resultUnrounded);
    }


    public function calculateWithdrawCommissionFeeBusinessClient(BusinessClient $client, \DateTimeImmutable $date, float $amount, string $currency): float|int
    {
        $client->withdraw($date, $amount);
        $resultUnrounded = $amount * $client->getWithdrawPercent();

        return $this->roundUp($resultUnrounded);
    }

    public function calculateDepositCommissionFee(BaseClient $client, string $amount):float
    {
        $resultUnrounded = $amount * $client->getDepositPercent();

        return $this->roundUp($resultUnrounded);
    }

    /**
     * This method calculates the diff betweeen the current withdraw and the
     * free limit (used only if currently below the limit!) and calculates the commission fee only on the exceeded amount.
     */
    private function calculateDiff($client, $prevAmount, $amount, $currency, $isConversionNeeded):float
    {
        if($isConversionNeeded) {
            $convertedFullAmount = $this->converter->convert($amount, $currency, $client::ACCOUNT_CURRENCY);
            $taxedAmount = ($prevAmount + $convertedFullAmount)
                - $client::FREE_LIMIT_WITHDRAW_AMOUNT;

            $taxedAmountToOrgCurrency = $this->converter
                ->convert($taxedAmount, $client::ACCOUNT_CURRENCY, $currency);
            $resultUnrounded = $taxedAmountToOrgCurrency
                * $client->getWithdrawPercent();

            return $this->roundUp($resultUnrounded);
        }

        $taxedAmount = ($prevAmount + $amount)
        - $client::FREE_LIMIT_WITHDRAW_AMOUNT;

        $resultUnrounded = $taxedAmount * $client->getWithdrawPercent();

        return $this->roundUp($resultUnrounded);
    }

    private function doWithdraw($client, $date, $amount, $currency, $isConversionNeeded):void
    {
        if($isConversionNeeded){
            $client->withdraw($date,
                $this->converter->convert($amount,
                    $currency,
                    $client::ACCOUNT_CURRENCY));
        } else {
            $client->withdraw($date, $amount);
        }
    }
}