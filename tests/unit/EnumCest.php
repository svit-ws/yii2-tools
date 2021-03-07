<?php

namespace svit\tools\tests\unit;

use svit\tools\helpers\NullHelper;
use svit\tools\tests\enums\Enum;
use svit\tools\tests\UnitTester;

class EnumCest
{
    public function options(UnitTester $I)
    {
        $I->assertEquals([
            'value 1' => 'Value 1',
            'value 2' => 'Value 2',
        ], Enum::getOptions());
    }

    public function optionsPrompt(UnitTester $I)
    {
        $I->assertEquals([
            NullHelper::VALUE => '',
            'value 1' => 'Value 1',
            'value 2' => 'Value 2',
        ], Enum::getOptions(null));
    }
}
