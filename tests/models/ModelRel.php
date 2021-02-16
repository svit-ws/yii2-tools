<?php

namespace svit\tools\tests\models;

use svit\tools\behaviors\RelationLinkBehavior;
use yii\db\ActiveRecord;

/**
 * Class ModelRel
 * @package svit\tools\tests\models
 *
 * @property int $id
 * @property int $rel_id
 * @property string $title
 *
 * @property ModelStub $stub
 * @property ModelStub[] $models
 */
class ModelRel extends ActiveRecord
{
    public static function tableName()
    {
        return 'relation';
    }

    public function behaviors()
    {
        return [
            [
                'class' => RelationLinkBehavior::class,
                'attributes' => ['models'],
            ],
        ];
    }

    public function getStub()
    {
        return $this->hasOne(ModelStub::class, ['id' => 'rel_id']);
    }

    public function getModels()
    {
        return $this->hasMany(ModelStub::class, ['id' => 'stub_id'])
            ->viaTable('junction', ['rel_id' => 'id']);
    }
}
