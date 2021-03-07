<?php

namespace svit\tools\helpers;

use yii\db\ActiveQuery;

class NullHelper
{
    const VALUE = 'null';

    public static function filter($value)
    {
        return $value === static::VALUE ? null : $value;
    }

    /**
     * @param $query ActiveQuery
     * @param $model
     * @param $fields string|array
     */
    public static function queryFilter(ActiveQuery $query, $model, $fields)
    {
        foreach ((array)$fields as $field) {
            if ($value = $model->$field) {
                $condition = [];
                if (is_array($value)) {
                    if (\yii\helpers\ArrayHelper::removeValue($value, static::VALUE)) {
                        $condition = [$field => null];
                    }

                    if ($value) {
                        $extra = [$field => $value];
                        $condition = $condition ? ['or', $condition, $extra] : $extra;
                    }
                } else {
                    $condition = [$field => ($value === static::VALUE ? null : $value)];
                }

                $query->andWhere($condition);
            }
        }
    }

    public static function option($options = [])
    {
        return [static::VALUE => ''] + $options;
    }
}
