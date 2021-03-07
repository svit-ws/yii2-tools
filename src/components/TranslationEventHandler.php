<?php

namespace svit\tools\components;

use yii\helpers\Inflector;
use yii\i18n\MissingTranslationEvent;

class TranslationEventHandler
{
    public static function handleMissingTranslation(MissingTranslationEvent $event)
    {
        $event->translatedMessage = static::dottedValue($event->message);
    }

    public static function dottedValue($text, $humanize = true)
    {
        if ($pos = strpos($text, '.')) {
            $text = substr($text, $pos + 1);
        }

        return $humanize ? Inflector::humanize($text) : $text;
    }

    public static function UCFirst(MissingTranslationEvent $event)
    {
        $event->translatedMessage = ucfirst($text = $event->message);
    }
}
