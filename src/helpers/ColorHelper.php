<?php

namespace svit\tools\helpers;

class ColorHelper
{
    protected static $colors = [
        '#0abb87', //success
        '#ffb822', //warning
        '#5d78ff', //info
        '#fd397a', //danger
        '#282a3c', //dark
        '#fd40fd', //violet
        '#42fafd', //blue
        '#52fd41', //green
        '#ffa9bf', //pink
    ];

    /**
     * Generates the color from predefined list
     * @return \Generator
     */
    public static function generator()
    {
        while (true) {
            foreach (static::$colors as $color) {
                yield $color;
            }
        }
    }

    public static function hex2rgba($hex, $alpha = 1)
    {
        list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");

        return "rgba({$r}, {$g}, {$b}, {$alpha})";
    }
}
