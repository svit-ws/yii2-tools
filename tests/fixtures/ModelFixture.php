<?php

namespace svit\tools\tests\fixtures;

use svit\tools\tests\models\Model;
use yii\test\ActiveFixture;

class ModelFixture extends ActiveFixture
{
    public $modelClass = Model::class;
}
