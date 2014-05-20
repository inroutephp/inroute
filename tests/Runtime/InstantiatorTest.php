<?php
namespace inroute\Runtime;

class InstantiatorTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateInstance()
    {
        $i = new Instantiator;

        $this->assertInstanceOf(
            'Exception',
            $i('Exception'),
            'Instantiator::__invoke must return an instance of supplied classname'
        );
    }
}
