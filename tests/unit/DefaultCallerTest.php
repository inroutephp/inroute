<?php
/**
 * This file is part of the inroute package
 *
 * Copyright (c) 2013 Hannes Forsgård
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iio\inroute;

class DefaultCallerTest extends \PHPUnit_Framework_TestCase
{
    public function testCall()
    {
        $route = $this->getMock(
            'iio\inroute\Route',
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
