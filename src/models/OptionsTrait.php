<?php

namespace svit\tools\models;

use svit\tools\helpers\NullHelper;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

trait OptionsTrait
{
    /**
     * @param array|string|null $config
     * [
     *  'from' => 'id',
     *  'to' => 'name',
     *  'cache' => 0, //duration in sec
     *  'null' => false, //null option
     *  'condition' => [],
     *  'sort' => [],
     * ]
     *
     * or
     *  'string' as config key 'to'
     *
     * or
     *  null as shortcut for ['null' => true]
     *
     * @return array
     */
    public static function getOptions($config = [])
    {
        $default = [
            'from' => 'id',
            'to' => 'name',
            'cache' => 0,
            'null' => false,
            'condition' => [],
            'sort' => [],
        ];

        if ($config === null) {
            $config = ['null' => true];
        } elseif (is_scalar($config)) {
            $config = ['to' => $config];
        }

        if (!isset($config['asArray'])) {
            $config['asArray'] = (isset($config['to']) && is_callable($config['to'])) ? false : true;
        }

        $config = $config + $default;

        $getOptions = function () use ($config) {
            /** @var ActiveQuery $query */
            $query = static::find()
                ->where($config['condition'])
                ->orderBy($config['sort']);

            $items = ArrayHelper::map($query->asArray($config['asArray'])->all(), $config['from'], $config['to']);

            if ($config['null']) {
                $items = NullHelper::option($items);
            }

            return $items;
        };

        return $config['cache']
            ? Yii::$app->cache->getOrSet([static::class, func_get_args()], $getOptions, $config['cache'])
            : $getOptions();
    }
}
