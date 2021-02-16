<?php

namespace svit\tools\helpers;

class ArrayHelper extends \yii\helpers\ArrayHelper
{
    /**
     * Searches array by callback
     * returns first satisfy element
     *
     * @param array $array
     * @param callable $callback
     * @return bool|mixed
     */
    public static function find(&$array, $callback)
    {
        foreach ($array as $item) {
            if (call_user_func($callback, $item)) {
                return $item;
            }
        }

        return false;
    }

    /**
     * Builds a map with array as keys
     * Many-to-many inverse relation php alternative
     *
     * Example:
     * ```php
     * $array = [
     *      ['id' => 1, 'values' => [1, 2]],
     *      ['id' => 2, 'values' => [2, 3]],
     * ];
     *
     * $result = ArrayHelper::mapMulti($array, 'values', 'id');
     * // [
     * //   1 => [1],
     * //   2 => [1, 2],
     * //   3 => [2],
     * // ]
     * ```
     *
     * @param array $array
     * @param string|\Closure $from represents array value
     * @param string|\Closure $to
     * @return array
     */
    public static function mapMulti($array, $from, $to)
    {
        $result = [];
        foreach ($array as $element) {
            $keys = (array)static::getValue($element, $from);
            $value = static::getValue($element, $to);
            foreach ($keys as $key) {
                $result[$key][$value] = 1;
            }
        }

        return array_map('array_keys', $result);
    }

    /**
     * Returns first word-based match value from array
     * by keys, indexed values and associated values
     * @param $needle
     * @param $haystack
     * @param null $param
     * @param bool $prefix
     * @return mixed|null
     * @see \common\tests\unit\ArrayHelperCest::matchWords
     */
    public static function matchWords($needle, $haystack, $param = null, $prefix = true)
    {
        $replacement = '\W+';
        $canMatchKey = static::isAssociative($haystack, false);

        foreach ($haystack as $key => $value) {
            if ($param && is_array($value)) {
                $subject = static::getValue($value, $param);
            } elseif ($canMatchKey) {
                $subject = $key;
            } else {
                $subject = $value;
            }

            if ($subject && is_scalar($subject)) {
                $pattern = preg_replace("#{$replacement}#", $replacement, $subject);
                if ($prefix) {
                    $pattern = '^' . $pattern;
                }

                if (preg_match("#{$pattern}#iu", $needle)) {
                    return $value;
                }
            }
        }

        return null;
    }
}
