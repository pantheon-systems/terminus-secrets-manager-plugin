<?php

namespace Pantheon\TerminusHello\Model;

use PHPUnit\Framework\TestCase;

class GreeterTest extends TestCase
{
    /**
     * Data provider for testGreeter.
     *
     * Return an array of arrays, each of which contains the parameter
     * values to be used in one invocation of the testGreeter test function.
     */
    public function greeterTestValues()
    {
        return [
            ['Hello, World!', 'hello', 'World', ],
            ['Good morning, Vietnam!', 'morning', 'Vietnam', ],
            ['Good evening, Mr. Bond!', 'evening', 'Mr. Bond', ],
        ];
    }

    /**
     * Test our Greeter class. Each time this function is called, it will
     * be passed data from the data provider function idendified by the
     * dataProvider annotation.
     *
     * @dataProvider greeterTestValues
     */
    public function testGreeter($expected, $constructor_parameter, $name)
    {
        $greeter = new Greeter($constructor_parameter);
        $this->assertEquals($expected, $greeter->render($name));
    }
}
