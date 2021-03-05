<?php

namespace svit\tools\behaviors;

use yii\base\Behavior;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class MapperBehavior
 *
 * Example:
 *
 * class ModelMap extends ActiveRecord
 * {
 *  ...
 *  public function behaviors()
 *  {
 *      return [
 *          ...
 *          [
 *              'class' => MapperBehavior::class,
 *              'filter' => [
 *                  'response' => [Json::class, 'decode'], // filter data before mapping
 *              ],
 *              'map' => [
 *                  'id' => '_id.$id', // ArrayHelper::getValue()
 *                  'click_id' => ['_get.click', '_post.click'], // queue try - if not _get try _post ...etc
 *                  'data' => function ($data) { // Closure
 *                      return <some calculations>;
 *                  },
 *              ],
 *          ],
 *      ...
 *      ];
 *  }
 *  ...
 * }
 *
 * $model = new ModelMap([
 *      'response' => '{"json encoded"}',
 *      '_id' => ['$id' => 'some value'],
 *      '_get' => ['click' => 'some value'],
 * ]);
 *
 * or
 *
 * $model->loadData([...]);
 *
 * @property Model $owner
 *
 * Note: must be last in behaviors list or refactor functionality, because of catch all values in __set
 */
class MapperBehavior extends Behavior
{
    public $map = [];
    public $filter = [];

    private $_data = [];

    public function events()
    {
        return [
            Model::EVENT_BEFORE_VALIDATE => 'mapData',
        ];
    }

    public function mapData()
    {
        foreach ($this->map as $attribute => $keys) {
            foreach ((array)$keys as $key) {
                if ($value = ArrayHelper::getValue($this->_data, $key)) {
                    $this->owner->$attribute = $value;
                    break;
                }
            }
        }
    }

    public function loadData($data)
    {
        foreach ($data as $name => $value) {
            $this->__set($name, $value);
        }
    }

    public function __set($name, $value)
    {
        $this->_data[$name] = isset($this->filter[$name])
            ? call_user_func($this->filter[$name], $value)
            : $value;
    }

    public function __isset($name)
    {
        return isset($this->_data[$name]) ? $this->_data[$name] : parent::__isset($name);
    }

    public function canSetProperty($name, $checkVars = true)
    {
        return true;
    }

    public function canGetProperty($name, $checkVars = true)
    {
        return $this->__isset($name) ?: parent::canGetProperty($name, $checkVars);
    }
}
