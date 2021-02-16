<?php

namespace svit\tools\components;

use DateInterval;
use DateTime;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class Formatter extends \yii\i18n\Formatter
{
    const TIMESTAMP = 'Y-m-d H:i:s';

    public function asTel($value)
    {
        $phone = '+' . $value;

        return Html::a($phone, "tel:$phone");
    }

    public function asSet($value)
    {
        return $value ? implode(', ', $value) : $this->nullDisplay;
    }

    public function asNumber($value)
    {
        return $this->asInteger((int) $value);
    }

    public function asValueOf($value, $items, $key = 'name')
    {
        if ($value && isset($items[$value]) && ($item = $items[$value])) {
            return is_scalar($item) ? $item : ArrayHelper::getValue($item, $key, $this->nullDisplay);
        }

        return $this->nullDisplay;
    }

    public function asMonth($value)
    {
        return $this->asDate($value, 'yyyy-MM');
    }

    public function asDurationTime($value, $implodeString = ', ', $negativeSign = '-')
    {
        $nullDisplay = '00:00:00';
        if (!$value) {
            return $nullDisplay;
        }

        if ($value instanceof DateInterval) {
            $isNegative = $value->invert;
            $interval = $value;
        } elseif (is_numeric($value)) {
            $isNegative = $value < 0;
            $zeroDateTime = (new DateTime())->setTimestamp(0);
            $valueDateTime = (new DateTime())->setTimestamp(abs($value));
            $interval = $valueDateTime->diff($zeroDateTime);
        } elseif (strncmp($value, 'P-', 2) === 0) {
            $interval = new DateInterval('P' . substr($value, 2));
            $isNegative = true;
        } else {
            $interval = new DateInterval($value);
            $isNegative = $interval->invert;
        }

        $parts = [];
        if ($interval->y > 0) {
            $parts[] = Yii::t('yii', '{delta}y', ['delta' => $interval->y], $this->language);
        }
        if ($interval->m > 0) {
            $parts[] = Yii::t('yii', '{delta}m', ['delta' => $interval->m], $this->language);
        }
        $h = $interval->h + $interval->d * 24;

        $parts[] = sprintf("%02d:%02d:%02d", $h, $interval->i, $interval->s);

        return empty($parts) ? $nullDisplay : (($isNegative ? $negativeSign : '') . implode($implodeString, $parts));
    }
}
