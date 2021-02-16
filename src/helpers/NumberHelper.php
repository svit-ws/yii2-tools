<?php

namespace svit\tools\helpers;

class NumberHelper
{
    public static function ceil($value, $base = 1, $shift = 0)
    {
        return ceil($value / $base) * $base + $shift;
    }
}
