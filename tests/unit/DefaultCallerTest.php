<?php
namespace itbz\inroute;

class DefaultCallerTest extends \PHPUnit_Framework_TestCase
{
    public function testCall()
    {
        $route = $this->getMock(
            'itbz\inroute\Route',
            array(),
            array(),
            '',
            false
        );

        $callbackcalled = false;
        $that = &$this;
        $callback = function ($param) use (&$callbackcalled, $that, $route) {
            $callbackcalled = true;
            $that->assertSame($param, $route);
        };

        $caller = new DefaultCaller();
        $caller->call($callback, $route);

        $this->assertTrue($callbackcalled);
    }
}
