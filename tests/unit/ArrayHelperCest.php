<?php

namespace svit\tools\tests\unit;

use Codeception\Example;
use svit\tools\helpers\ArrayHelper;
use svit\tools\tests\UnitTester;

class ArrayHelperCest
{
    public function find(UnitTester $I)
    {
        $arr = [(object)1, (object)2, (object)3];

        $item = ArrayHelper::find($arr, function ($obj) {
           return $obj->scalar === 2;
        });
        $I->assertEquals(2, $item->scalar);

        $item->scalar++;
        $I->assertEquals(3, $arr[1]->scalar);
    }

    public function mapMulti(UnitTester $I)
    {
        $array = [
            ['id' => 1, 'values' => [1, 2]],
            ['id' => 2, 'values' => [2, 3]],
        ];
        $I->assertEquals([
            1 => [1],
            2 => [1, 2],
            3 => [2],
        ], ArrayHelper::mapMulti($array, 'values', 'id'));

    }

    /**
     * @param UnitTester $I
     * @param Example $example
     * @dataProvider cases
     */
    public function matchWords(UnitTester $I, Example $example)
    {
        $I->assertEquals($example['expected'], ArrayHelper::matchWords(...$example['args']), $example['message']);
    }

    protected function cases()
    {
        $actual = 'Some actual Value, may be with 12345 numbers or many   spaces or    tabs';
        $haystack = [
            'test key' => 'test value',
            'key2' => ['one' => 1, 'two' => 2, 'three' => $actual],
            $actual => $actual,
            'key3' => [1, $actual, 3],
        ];

        $needle = 'Some Actual "VALUE", may be with 12345 numbers or    many spaces or       tabs and RaNdOm postfix';
        $prefixed = 'any random prefix ' . $needle;

        return [
            [
                'args' => [
                    $needle,
                    $haystack,
                ],
                'expected' => $actual,
                'message' => 'search in keys',
            ],
            [
                'args' => [
                    $prefixed,
                    $haystack,
                    null,
                    false
                ],
                'expected' => $actual,
                'message' => 'search in keys any match',
            ],
            [
                'args' => [
                    $needle,
                    $haystack,
                    'three',
                ],
                'expected' => $haystack['key2'],
                'message' => 'search in values by param',
            ],
            [
                'args' => [
                    $needle,
                    array_values($haystack),
                ],
                'expected' => $actual,
                'message' => 'search in indexed scalar values',
            ],
        ];
    }
}
