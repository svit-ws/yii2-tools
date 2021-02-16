<?php

namespace svit\tools\tests\unit;

use svit\tools\behaviors\DbSetBehavior;
use svit\tools\components\Formatter;
use svit\tools\tests\fixtures\ModelFixture;
use svit\tools\tests\fixtures\RelationFixture;
use svit\tools\tests\models\ModelStub;
use svit\tools\tests\UnitTester;
use yii\helpers\Json;

class BehaviorCest
{
    public function _fixtures()
    {
        return [
            ModelFixture::class,
        ];
    }

    public function dbSet(UnitTester $I)
    {
        $model = ModelStub::findOne(1);
        $I->assertNotEmpty($model);
        $I->assertIsArray($model->list);

        $model->list = array_merge($model->list, ['new value']);
        $I->assertTrue($model->save());
        $I->assertIsArray($model->list);
        $I->seeRecord(ModelStub::class, [
            'id' => $model->id,
            'list' => implode(DbSetBehavior::DELIMITER, $model->list),
        ]);
    }

    public function timestamp(UnitTester $I)
    {
        $timestamp = (new \DateTime())->format(Formatter::TIMESTAMP);

        $model = new ModelStub([
            'list' => 'list',
            'body' => 'body',
        ]);
        $I->assertTrue($model->save());
        $I->assertGreaterOrEquals($timestamp, $model->created_at);
    }

    public function mapper(UnitTester $I)
    {
        $list = ['one', 'two', 'free'];
        $data = [
            '_id' => 555,
            '_get' => [
                'body' => 'some data',
            ],
            'encoded' => Json::encode($list),
        ];

        $model = new ModelStub($data);
        $I->assertTrue($model->validate());
        $I->assertEquals($data['_id'], $model->id);
        $I->assertEquals($data['_get']['body'], $model->body);
        $I->assertEquals($list, $model->list);
    }

    public function relationLink(UnitTester $I)
    {
        $I->haveFixtures([
            RelationFixture::class,
        ]);
        $data = [
            'relations' => [2, 3],
        ];
        $model = new ModelStub();
        $I->assertTrue($model->load($data, ''));
        $I->assertTrue($model->save());

        $model->refresh();
        $I->assertIsArray($model->relations);
        $I->assertCount(2, $model->relations);
    }

    public function relationLinkJunction(UnitTester $I)
    {
        $I->haveFixtures([
            'relations' => RelationFixture::class,
        ]);
        $data = [
            'models' => [1],
        ];
        $model = $I->grabFixture('relations', 0);
        $I->assertTrue($model->load($data, ''));
        $I->assertTrue($model->save());

        $model->refresh();
        $I->assertIsArray($model->models);
        $I->assertCount(1, $model->models);
        $I->assertInstanceOf(ModelStub::class, $model->models[0]);
        $I->assertEquals(1, $model->models[0]->id);
    }
}
