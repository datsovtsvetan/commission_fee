<?php

namespace App\Interfaces;

interface CurrencyConverterInterface
{
    public function convert(float $amount, string $from, string $to):float;
}