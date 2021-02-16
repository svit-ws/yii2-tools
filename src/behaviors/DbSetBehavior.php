<?php

namespace svit\tools\behaviors;

use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;

/**
 * Class DbSetBehavior
 * @property ActiveRecord $owner
 */
class DbSetBehavior extends Behavior
{
    const DELIMITER = ',';

    public $attributes;

    public function init()
    {
        if (empty($this->attributes)) {
            throw new InvalidConfigException('parameter `attributes` is required');
        }

        $this->attributes = (array)$this->attributes;
    }

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'afterFind',
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
        ];
    }

    public function afterFind()
    {
        foreach ($this->attributes as $attribute) {
            $this->owner->$attribute = static::setToArray($this->owner->getAttribute($attribute));
        }
    }

    public function beforeSave($event)
    {
        if ($event->name == ActiveRecord::EVENT_BEFORE_UPDATE && empty($this->owner->dirtyAttributes)) {
            return;
        }

        foreach ($this->attributes as $attribute) {
            if (is_array($this->owner->$attribute)) {
                $this->owner->$attribute = implode(self::DELIMITER, $this->owner->$attribute);
            }
        }
    }

    public function afterSave()
    {
        $this->owner->refresh();
    }

    /**
     * @param $value
     * @return array|false|string[]
     */
    public static function setToArray($value)
    {
        return $value
            ? explode(self::DELIMITER, $value)
            : [];
    }
}
