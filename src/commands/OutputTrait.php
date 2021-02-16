<?php

namespace svit\tools\commands;

use Yii;
use yii\helpers\Console;

trait OutputTrait
{
    public function output($string, ...$args)
    {
        if (Yii::$app->controller->interactive) {
            $string = Console::ansiFormat($string, $args);
        }

        return Console::output($string);
    }

    public function progress($total)
    {
        $done = 0;
        Yii::$app->controller->interactive && Console::startProgress($done, $total);

        $start = microtime(true);
        while (true) {
            $value = yield;

            if ($value === null) {
                Yii::$app->controller->interactive && Console::endProgress();
                break;
            }

            Yii::$app->controller->interactive && Console::updateProgress(++$done, $total, self::progressPrefix($value));
        }

        $elapsed = microtime(true) - $start;
        $perUnit = $elapsed / $total;
        $output = sprintf('Time elapsed %f sec / Avg per unit %f sec', $elapsed, $perUnit);
        $this->output($output, Console::ITALIC, Console::FG_GREY);
    }

    private static function progressPrefix($lastValue)
    {
        return "Last value {$lastValue}";
    }
}
