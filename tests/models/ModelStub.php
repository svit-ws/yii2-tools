<?php

namespace svit\tools\tests\models;

use svit\tools\behaviors\DbSetBehavior;
use svit\tools\behaviors\MapperBehavior;
use svit\tools\behaviors\RelationLinkBehavior;
use svit\tools\behaviors\TimestampBehavior;
use yii\helpers\Json;

/**
 * Class ModelStub
 * @package svit\tools\tests
 *
 * @property ModelRel[] $relations
 */
class ModelStub extends Model
{
    public function behaviors()
    {
        return [
            [
                'class' => DbSetBehavior::class,
                'attributes' => 'list',
            ],
            [
                'class' => TimestampBehavior::class,
            ],
            [
                'class' => RelationLinkBehavior::class,
                'attributes' => ['relations'],
                'delete' => false,
            ],
            [
                'class' => MapperBehavior::class,
                'filter' => [
                    'encoded' => [Json::class, 'decode'],
                ],
                'map' => [
                    'id' => '_id',
                    'body' => ['_get.body', '_post.body'],
                    'list' => function ($data) {
                        return isset($data['encoded']) ? array_values($data['encoded']) : null;
                    },
                ],
            ],
        ];
    }

    public function getRelations()
    {
        return $this->hasMany(ModelRel::class, ['rel_id' => 'id']);
    }
}
