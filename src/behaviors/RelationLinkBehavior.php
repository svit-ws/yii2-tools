<?php

namespace svit\tools\behaviors;

use yii\base\Behavior;
use yii\base\InvalidArgumentException;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\validators\SafeValidator;

/**
 * Class RelationLinkBehavior
 *
 * @property ActiveRecord $owner
 */
class RelationLinkBehavior extends Behavior
{
    public $attributes;
    public $delete = true;
    private $_links = [];

    public function init()
    {
        if (empty($this->attributes) || !is_array($this->attributes)) {
            throw new InvalidArgumentException('attributes[] list is empty');
        }
    }

    public function attach($owner)
    {
        parent::attach($owner);

        $this->owner->validators->append(new SafeValidator(['attributes' => $this->attributes]));
    }

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_UPDATE => 'eventAfterSave',
            ActiveRecord::EVENT_AFTER_INSERT => 'eventAfterSave',
        ];
    }

    public function eventAfterSave()
    {
        foreach ($this->_links as $link => $values) {
            $this->owner->unlinkAll($link, $this->delete);
            $relation = $this->owner->getRelation($link);
            /** @var ActiveRecord $class */
            $class = $relation->modelClass;
            $models = $class::findAll($values);

            foreach ($models as $model) {
                $this->owner->link($link, $model, $this->getExtraFields($relation));
            }
        }
    }

    private function getExtraFields($relation)
    {
        $fields = [];
        if ($on = ArrayHelper::getValue($relation->via ?: $relation, 'on')) {
            foreach ($on as $key => $value) {
                $key = ($pos = strrpos($key, '.')) ? substr($key, ($pos + 1)) : $key;
                $fields[$key] = $value;
            }
        }

        return $fields;
    }

    public function getLink($name)
    {
        return (in_array($name, $this->attributes) && isset($this->_links[$name])) ? $this->_links[$name] : null;
    }

    public function __set($name, $value)
    {
        if (in_array($name, $this->attributes)) {
            $this->_links[$name] = $value;
        } else {
            parent::__set($name, $value);
        }
    }

    public function canSetProperty($name, $checkVars = true)
    {
        return in_array($name, $this->attributes) || parent::canSetProperty($name, $checkVars);
    }
}
