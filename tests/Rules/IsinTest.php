<?php

namespace Intervention\Validation\Test\Rules;

use Intervention\Validation\Rules\Isin;
use Intervention\Validation\Validator;
use PHPUnit\Framework\TestCase;

class IsinTest extends TestCase
{
    /**
     * @dataProvider dataProvider
    */
    public function testValidation($result, $value)
    {
        $validator = new Validator([new Isin()]);
        $this->assertEquals($result, $validator->validate($value));
    }

    public function dataProvider()
    {
        return [
            [true, 'US0378331005'],
            [true, 'DE0005810055'],
            [false, 'DE0005810058'],
            [false, 'ZA9382189201'],
            [false, 'x'],
        ];
    }
}
