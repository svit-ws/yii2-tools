<?php

namespace svit\tools\enums;

use ReflectionClass;
use ReflectionClassConstant;
use ReflectionMethod;
use svit\tools\helpers\ArrayHelper;
use svit\tools\helpers\DbHelper;
use svit\tools\helpers\NullHelper;
use svit\tools\models\GridOptionsInterface;
use Yii;
use yii\helpers\Html;
use yii\helpers\Inflector;

/**
 * Description of AbstractEnum
 */
abstract class AbstractEnum implements GridOptionsInterface
{
    private function __construct()
    {
    }

    /**
     * @param int $modifiers
     * @return ReflectionClassConstant[]
     */
    protected static function getConstants($modifiers = ReflectionMethod::IS_PUBLIC)
    {
        static $constCacheArray = [];

        try {
            $calledClass = get_called_class();

            if (!array_key_exists($calledClass, $constCacheArray)) {
                $reflect = new ReflectionClass($calledClass);
                $constCacheArray[$calledClass] = $reflect->getReflectionConstants();
            }

            return array_filter($constCacheArray[$calledClass], function (ReflectionClassConstant $item) use ($modifiers) {
                return (bool)($item->getModifiers() & $modifiers);
            });
        } catch (\Throwable $exception) {
            Yii::error($exception->getMessage());
        }

        return [];
    }

    protected static function addPrefix($value = null)
    {
        if ($value === null) {
            return $value;
        }

        $classname = get_called_class();

        if ($pos = strrpos($classname, '\\')) {
            $classname = substr($classname, $pos + 1);
        }

        return $classname . '.' . $value;
    }

    public static function htmlClassMap()
    {
        return [];
    }

    public static function getHtmlClass($key, $prefix = 'badge')
    {
        $class = static::htmlClassMap()[$key] ?? $key;

        if ($prefix) {
            $class = "$prefix $class";
        }

        return Inflector::slug($class);
    }

    /**
     * @return array all const values
     */
    public static function getValues()
    {
        return array_map(function (ReflectionClassConstant $item) {
            return $item->getValue();
        }, static::getConstants());
    }

    /**
     * @param bool $translate
     * @return array (const value => const name)
     */
    public static function getNames($translate = false)
    {
        $arr = ArrayHelper::map(static::getConstants(), function (ReflectionClassConstant $item) {
            return $item->getValue();
        }, 'name');

        if ($translate) {
            $arr = array_map('static::getValue', $arr);
        }

        return $arr;
    }

    /**
     * @param $key
     * @param string $postfix
     * @return string translated value
     */
    public static function getValue($key, $postfix = '')
    {
        return Yii::t('enum', static::addPrefix($key)) . $postfix;
    }

    /**
     * key => translated value helper
     * useful for select tag options
     *
     * @param array|null $config
     *  ['null' => bool]
     * or
     *  null - as shortcut for ['null' => true]
     *
     * @return array
     */
    public static function getOptions($config = [])
    {
        $items =  ArrayHelper::map(static::getConstants(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED), function (ReflectionClassConstant $item) {
            return $item->getValue() ?? NullHelper::VALUE;
        }, function (ReflectionClassConstant $item) {
            return static::getValue($item->getValue());
        });

        if ($config === null || !empty($config['null'])) {
            $items = NullHelper::option($items);
        }

        return $items;
    }

    public static function match($value)
    {
        static $map;

        if ($map === null) {
            $map = array_flip(static::getOptions());
        }

        return ArrayHelper::matchWords($value, $map) ?: $value;
    }

    public static function getLabels()
    {
        $result = [];
        foreach (static::getOptions() as $key => $value) {
            $result[$key] = static::getLabel($key, $value);
        }

        return $result;
    }

    /**
     * Labeled translated value
     *
     * @param $key
     * @param null $value
     * @return string
     */
    public static function getLabel($key, $value = null)
    {
        $class = ['badge', static::getHtmlClass($key)];
        $value = $value ?: static::getValue($key);

        return Html::tag('span', $value, ['class' => $class, 'data-value' => $key]);
    }

    /**
     * Helper for db ENUM/SET type field migrations
     *
     * @param bool $default
     * @param string $type
     * @return string
     */
    public static function getType($default = false, $type = 'ENUM')
    {
        $values = array_map(function ($item) {
            return "'{$item}'";
        }, static::getValues());

        $result = implode(', ', $values);
        $result = "{$type}({$result})";

        if ($default !== false) {
            $result .= " DEFAULT '{$default}'";
        }

        return $result;
    }

    /**
     * SQL PIVOT helper
     *
     * @param $table
     * @param $field
     * @param array $select
     * @param int $value
     * @param string $prefix
     * @return array
     */
    public static function selectPivot($table, $field, &$select = [], $value = 1, $prefix = '')
    {
        return DbHelper::selectPivot(static::getValues(), $table, $field, $select, $value, $prefix);
    }

    /**
     * Grid widget helper for ENUM
     *
     * @param $field
     * @param array|string $options
     * @return array
     */
    public static function gridColumn($field, $options = [])
    {
        if (is_scalar($options)) {
            $options = [
                'label' => $options,
            ];
        }

        $values = static::getOptions();
        $options = $options + [
                'attribute' => $field,
                'filter' => $values,
                'value' => function ($model) use ($field) {
                    $values = array_map('static::getLabel', (array)ArrayHelper::getValue($model, $field));

                    return implode(PHP_EOL, $values);
                },
                'format' => 'raw',
            ];

        return $options;
    }

    /**
     * Detailed widget helper for ENUM
     *
     * @param $field
     * @return array
     */
    public static function detailAttribute($field)
    {
        return [
            'attribute' => $field,
            'value' => function ($model) use ($field) {
                return static::getLabel($model->$field);
            },
            'format' => 'raw',
        ];
    }

    /**
     * @return array
     * @deprecated use getOptions() instead
     */
    public static function getClientValues()
    {
        return static::getOptions();
    }

    /**
     * @param $key
     * @return string
     * @deprecated use getValue() instead
     */
    public static function getClientValue($key)
    {
        return static::getValue($key);
    }

    /**
     * @return array
     * @deprecated use getLabels() instead
     */
    public static function getLabeledClientValues()
    {
        return static::getLabels();
    }

    /**
     * @param $key
     * @param null $value
     * @return string
     * @deprecated use getLabel() instead
     */
    public static function getLabeledClientValue($key, $value = null)
    {
        return static::getLabel($key, $value);
    }
}
