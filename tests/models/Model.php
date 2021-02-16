<?php

namespace svit\tools\tests\models;

/**
 * Class Model
 * @package svit\tools\tests\models
 *
 * @property int $id
 * @property array $list
 * @property string $created_at
 * @property string $updated_at
 * @property string $status
 * @property string $body
 */
class Model extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'model';
    }
}
