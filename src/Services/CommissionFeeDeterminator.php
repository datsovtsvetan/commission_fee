<?php

namespace App\Services;

class CommissionFeeDeterminator
{

    private function roundUp ( float $value, int $precision = 2):float
    {
        $pow = \pow( 10, $precision );
        return ( \ceil( $pow * $value ) + \ceil( $pow * $value - \ceil( $pow * $value ))) / $pow;
    }
}