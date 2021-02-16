<?php

namespace svit\tools\helpers;

use Yii;
use yii\helpers\Html;

class GridHelper
{
    public static function shortenTextColumn($attribute, $max_length = 25)
    {
        Yii::$app->view->registerJs('$(\'[data-toggle="tooltip-shorten-column"]\').tooltip()');

        $style = <<<CSS
        .tooltip-inner { text-align: left; }
        
        *[data-toggle=tooltip-shorten-column] {
            display: -webkit-box;
            height: 90px;
            margin: 0 auto;
            line-height: 1.4;
            -webkit-line-clamp: 5;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }
CSS;
        Yii::$app->view->registerCss($style);

        return [
            'attribute' => $attribute,
            'format' => 'html',
            'content' => function ($model) use ($attribute, $max_length) {
                return Html::tag(
                    'span',
                    $model->$attribute,
                    [
                        'data-toggle' => 'tooltip-shorten-column',
                        'title' => $model->$attribute
                    ]
                );
            }
        ];
    }
}
