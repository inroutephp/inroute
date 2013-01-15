<?php
namespace itbz\inroute;

class InrouteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException itbz\inroute\Exception\RuntimeExpection
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
