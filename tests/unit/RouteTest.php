<?php
namespace iio\inroute;

class RouteTest extends \PHPUnit_Framework_TestCase
{
    public function testGenerateRoute()
    {
        $auraroute = $this->getMock(
            'Aura\Router\Route'
        );
        $map = $this->getMock(
            'Aura\Router\Map',
            array(),
            array(),
            '',
            false
        );
        $route = new Route($auraroute, $map);

        $auraroute->expects($this->once())
            ->method('generate');

        $route->generate();
    }

    public function testGenerateMap()
    {
        $auraroute = $this->getMock(
            'Aura\Router\Route'
        );
        $map = $this->getMock(
            'Aura\Router\Map',
            array(),
            array(),
            '',
            false
        );
        $route = new Route($auraroute, $map);

        $map->expects($this->once())
            ->method('generate')
            ->with('name', array());

        $route->generate('name', array());
    }

    public function testGetName()
    {
        $auraroute = $this->getMock(
            'Aura\Router\Route'
        );
        $map = $this->getMock(
            'Aura\Router\Map',
            array(),
            array(),
            '',
            false
        );
        $route = new Route($auraroute, $map);

        $auraroute->expects($this->once())
            ->method('__get')
            ->with('name')
            ->will($this->returnValue('foobar'));

        $this->assertEquals('foobar', $route->getName());
    }

    public function testGetPath()
    {
        $auraroute = $this->getMock(
            'Aura\Router\Route'
        );
        $map = $this->getMock(
            'Aura\Router\Map',
            array(),
            array(),
            '',
            false
        );
        $route = new Route($auraroute, $map);

        $auraroute->expects($this->once())
            ->method('__get')
            ->with('path')
            ->will($this->returnValue('foobar'));

        $this->assertEquals('foobar', $route->getPath());
    }

    public function testGetValue()
    {
        $auraroute = $this->getMock(
            'Aura\Router\Route'
        );
        $map = $this->getMock(
            'Aura\Router\Map',
            array(),
            array(),
            '',
            false
        );
        $route = new Route($auraroute, $map);

        $auraroute->expects($this->once())
            ->method('__get')
            ->with('values')
            ->will($this->returnValue(array('name' => 'foobar')));

        $this->assertEquals('foobar', $route->getValue('name'));
    }

    public function testGetMethods()
    {
        $auraroute = $this->getMock(
            'Aura\Router\Route'
        );
        $map = $this->getMock(
            'Aura\Router\Map',
            array(),
            array(),
            '',
            false
        );
        $route = new Route($auraroute, $map);

        $auraroute->expects($this->once())
            ->method('__get')
            ->with('values')
            ->will($this->returnValue(array('method' => array('GET'))));

        $this->assertEquals(array('GET'), $route->getMethods());
    }
}
