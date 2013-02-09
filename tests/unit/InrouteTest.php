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

class InrouteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException iio\inroute\Exception\RuntimeExpection
     */
    public function testNoRouteException()
    {
        $map = $this->getMock(
            'Aura\Router\Map',
            array(),
            array(),
            '',
            false
        );

        $map->expects($this->once())
            ->method('match')
            ->will($this->returnValue(null));

        $inroute = new Inroute($map);
        $inroute->dispatch('/', array());
    }

    public function testDispatch()
    {
        $map = $this->getMock(
            'Aura\Router\Map',
            array(),
            array(),
            '',
            false
        );

        $visitedClosure = false;

        $route = $this->getMock(
            'Aura\Router\Route',
            array('isMatch'),
            array(
                null,
                null,
                null,
                array(
                    'controller' => function () use (&$visitedClosure) {
                        $visitedClosure = true;
                    }
                )
            )
        );

        $map->expects($this->once())
            ->method('match')
            ->will($this->returnValue($route));

        $inroute = new Inroute($map);
        $inroute->dispatch('/', array());

        $this->assertTrue(
            $visitedClosure,
            'Controller closure must be executed'
        );
    }
}
