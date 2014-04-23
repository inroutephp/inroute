<?php
namespace inroute\Compiler;

use inroute\Router\Route;
use inroute\Router\Regex;

class RouteFactoryTest extends \PHPUnit_Framework_TestCase
{
    function testVoid(){}

    public function createRoute()
    {
        // Detta test kan inte köras längre, eftersom det kräver DefinitionFinderOld...
        $closure = function(){};

        $routeDef = array(
            'controller'       => 'controller',
            'controllerMethod' => 'method',
            'httpmethods'      => array('GET'),
            'path'             => '/root/foo/{:name}'
        );

        $tokenizer = $this->getMock('inroute\Compiler\Tokenizer');

        $tokenizer->expects($this->once())
            ->method('tokenize')
            ->with($routeDef['path'])
            ->will($this->returnValue(array()));

        $tokenizer->expects($this->once())
            ->method('getRegex')
            ->will($this->returnValue(new Regex));

        $factory = new RouteFactory($closure, $tokenizer);

        $extractor = $this->getMockBuilder('inroute\Compiler\DefinitionFinderOld')
            ->disableOriginalConstructor()
            ->getMock();

        $extractor->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator(array($routeDef))));

        $factory->addRoutes($extractor);

        $expected = array(
            new Route(
                array(),
                new Regex,
                $routeDef['httpmethods'],
                $routeDef['controller'],
                $routeDef['controllerMethod'],
                $closure
            )
        );

        $this->assertEquals($expected, $factory->getRoutes());
    }
}
