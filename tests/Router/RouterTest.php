<?php
namespace inroute\Router;

class RouterTest extends \PHPUnit_Framework_TestCase
{
    public function testVoid()
    {
        $r = new Router(array());
    }
}
