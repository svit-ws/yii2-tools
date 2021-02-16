<?php

namespace svit\tools\validators;

use yii\validators\DateValidator;

class MonthValidator extends DateValidator
{
    public $format = 'yyyy-MM';
    public $timestampAttributeFormat = 'yyyy-MM-dd';

    public function validateAttribute($model, $attribute)
    {
        if (!$this->timestampAttribute) {
            $this->timestampAttribute = $attribute;
        }

        return parent::validateAttribute($model, $attribute);
    }
}
