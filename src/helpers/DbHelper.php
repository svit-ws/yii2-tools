<?php

namespace svit\tools\helpers;

use Yii;
use yii\helpers\ArrayHelper;

class DbHelper
{
    public static function MigrationENUM($arr, $default = null)
    {
        $result = 'ENUM(\'' . implode("', '", $arr) . '\')';

        if (!empty($default)) {
            $result .= ' DEFAULT \'' . $default . '\'';
        }

        return $result;
    }

    /**
     * SQL PIVOT helper
     *
     * @param array $values
     * @param string $table
     * @param string $field
     * @param array $select
     * @param int $term
     * @param string $prefix
     * @return array
     */
    public static function selectPivot($values, $table, $field, &$select = [], $term = 1, $prefix = '')
    {
        if (!is_numeric($term)) {
            $term = Yii::$app->db->quoteColumnName($term);
        }

        $isAssoc = ArrayHelper::isAssociative($values);

        foreach ($values as $key => $value) {
            $index = $isAssoc ? $key : ($prefix . $value);
            $select[$index] = "SUM(IF(`$table`.$field = '$value', $term, 0))";
        }

        return $select;
    }

    public static function selectPivotCount($values, $field, &$select = [], $term = 'id', $distinct = false, $prefix = '')
    {
        if (!is_numeric($term)) {
            $term = Yii::$app->db->quoteColumnName($term);
        }

        $isAssoc = ArrayHelper::isAssociative($values);
        $field = Yii::$app->db->quoteColumnName($field);
        $distinct = $distinct ? 'DISTINCT ' : '';

        foreach ($values as $key => $value) {
            $index = $isAssoc ? $key : ($prefix . $value);
            $value = Yii::$app->db->quoteValue($value);
            $select[$index] = "COUNT({$distinct}IF({$field} = {$value}, {$term}, NULL))";
        }

        return $select;
    }

    /**
     * @param $field
     * @param array $map
     * @param bool $filter
     * @return string
     */
    public static function map($field, $map, $filter = false)
    {
        if (!$map || ($filter && !($map = array_filter($map)))) {
            return $field;
        }

        $db = Yii::$app->db;

        $parts = ['CASE', $db->quoteColumnName($field)];
        foreach ($map as $actual => $mapped) {
            $actual = $db->quoteValue($actual);
            $mapped = $db->quoteValue($mapped);
            $parts[] = $filter ? "WHEN {$mapped} THEN {$actual}" : "WHEN {$actual} THEN {$mapped}";
        }
        $parts[] = 'END';

        return implode(PHP_EOL, $parts);
    }
}
