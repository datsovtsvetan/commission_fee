<?php

namespace App\Services;

use App\Interfaces\CurrencyConverterInterface;
use App\Interfaces\CommissionFeeCalculatorInterface;
use App\Model\BaseClient;
use App\Model\BusinessClient;
use App\Model\PrivateClient;

class CommissionFeeMyCustomCalculator implements CommissionFeeCalculatorInterface
{
    private CurrencyConverterInterface $converter;

    /**
     * Add more currencies (as special case) that are not with
     * the default 2 decimal places i.e. like 'JPY'.
     * If a currency is not found here, the currency will default with
     * 2 decimal places in roundUp() method i.e. 0.25.
     * So here 'EUR' and 'USD' being set with the default value (=> 2) are
     * added only as example.
     */
    private array $currenciesDecimalPoints = ['EUR' => 2, 'USD' => 2, 'JPY' => 0];

    public function __construct(CurrencyConverterInterface $customConverter)
    {
        $this->converter = $customConverter;
    }

    public function calculateWithdrawCommissionFeePrivateClient(
        PrivateClient $client,
        \DateTimeImmutable $date,
        float $amount,
        string $currency): float|int
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

        if($isUnderFreeLimitAmount){
            return $this->calculateDiff(
                $client,
                $prevAmount,
                $amount,
                $currency,
                $isConversionNeeded);
        }

        $resultUnrounded = $amount
            * $client->getWithdrawPercent();

        return $this->roundUp($resultUnrounded, $currency);
    }

    public function calculateWithdrawCommissionFeeBusinessClient(
        BusinessClient $client,
        \DateTimeImmutable $date,
        float $amount,
        string $currency): float|int
    {
        $client->withdraw($date, $amount);
        $resultUnrounded = $amount * $client->getWithdrawPercent();

        return $this->roundUp($resultUnrounded, $currency);
    }

    public function calculateDepositCommissionFee(
        BaseClient $client,
        string $amount,
        string $currency):float
    {
        $resultUnrounded = $amount * $client->getDepositPercent();

        return $this->roundUp($resultUnrounded, $currency);
    }

    /**
     * This method calculates the diff betweeen the current withdraw and the
     * free limit (used only if currently below the limit!) and calculates
     * the commission fee only on the exceeded amount.
     */
    private function calculateDiff(
        $client,
        $prevAmount,
        $amount,
        $currency,
        $isConversionNeeded):float
    {

        if($isConversionNeeded) {
            $convertedFullAmount = $this->converter
                ->convert($amount, $currency, $client::ACCOUNT_CURRENCY);
            $taxedAmount = ($prevAmount + $convertedFullAmount)
                - $client::FREE_LIMIT_WITHDRAW_AMOUNT;

            $taxedAmountToOrgCurrency = $this->converter
                ->convert($taxedAmount, $client::ACCOUNT_CURRENCY, $currency);

            $resultUnrounded = $taxedAmountToOrgCurrency
                * $client->getWithdrawPercent();

            return $this->roundUp($resultUnrounded, $currency);
        }

        $taxedAmount = ($prevAmount + $amount)
        - $client::FREE_LIMIT_WITHDRAW_AMOUNT;

        $resultUnrounded = $taxedAmount * $client->getWithdrawPercent();

        return $this->roundUp($resultUnrounded, $client::ACCOUNT_CURRENCY);
    }

    private function doWithdraw(
        $client,
        $date,
        $amount,
        $currency,
        $isConversionNeeded):void
    {
        if($isConversionNeeded){
            $client->withdraw($date,
                $this->converter->convert(
                    $amount,
                    $currency,
                    $client::ACCOUNT_CURRENCY));
        } else {
            $client->withdraw($date, $amount);
        }
    }

    /**
     * This method rounds always up, i.e. 0.21 becomes 0.3
     * If a currency has not a decimal point like 'JPY', it rounds up
     * to next whole integer value, i.e. 123.1 becomes 124 (but the
     * return type is still float though).
     * If a currency is not found (as special case)
     * in $this->currenciesDecimalPoint[], the default precision is 2.
     * i.e. 0.25
     */
    private function roundUp(float $value, string $currency, int $precision = 2): float
    {
        $currency = trim(strtoupper($currency));

        if(isset($this->currenciesDecimalPoints[$currency])
            && $this->currenciesDecimalPoints[$currency] == 0){
            return ceil($value);
        }

        if(isset($this->currenciesDecimalPoints[$currency])
            && $this->currenciesDecimalPoints[$currency] != 0){
            $precision = $this->currenciesDecimalPoints[$currency];
        }

        $pow = pow(10, $precision);
        return (ceil($pow * $value)
                + ceil($pow * $value - ceil($pow * $value))) / $pow;
    }
}