<?php

namespace svit\tools\behaviors;

use svit\tools\components\Formatter;

/**
 * Redefine returned value for behaviour - date as string instead of timestamp
 * because we use TIMESTAMP field type for dates instead integer
 */
class TimestampBehavior extends \yii\behaviors\TimestampBehavior
{
    protected function getValue($event)
    {
        if ($this->value === null) {
            return date(Formatter::TIMESTAMP);
        }

        return parent::getValue($event);
    }
}
