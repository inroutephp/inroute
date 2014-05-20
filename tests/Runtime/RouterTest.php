<?php
namespace inroute\Runtime;

class RouterTest extends \PHPUnit_Framework_TestCase
{
    public function testVoid()
    {
        new Router(array());
    }
}
