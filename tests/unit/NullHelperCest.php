<?php

namespace svit\tools\tests\unit;

use Codeception\Example;
use svit\tools\helpers\NullHelper;
use svit\tools\tests\fixtures\ModelFixture;
use svit\tools\tests\models\Model;
use svit\tools\tests\UnitTester;

class NullHelperCest
{
    public function _fixtures()
    {
        return [
            [
                'class' => ModelFixture::class,
                'dataFile' => codecept_data_dir('modelNullHelper.php'),
            ],
        ];
    }

    /**
     * @param UnitTester $I
     * @param Example $example
     *
     * @example { "data": null, "result": 3 }
     * @example { "data": {"status": "null"}, "result": 1 }
     * @example { "data": {"status": "new"}, "result": 1 }
     * @example { "data": {"status": ["null"]}, "result": 1 }
     * @example { "data": {"status": ["null", "new", "some"]}, "result": 3 }
     */
    public function queryFilter(UnitTester $I, Example $example)
    {
        $model = new Model($example['data']);
        $query = $model::find();
        NullHelper::queryFilter($query, $model, 'status');

        $I->assertEquals($example['result'], $query->count());
    }
}
