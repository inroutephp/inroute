<?php
namespace itbz\inroute;

class RouterGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /*public function testAddClasses()
    {
        $mustache = $this->getMock('\Mustache_Engine');
        $scanner = $this->getMock(
            'itbz\inroute\ClassScanner',
            array(),
            array(),
            '',
            false
        );
        $scanner->expects($this->once())
            ->method('getClasses')
            ->will($this->returnValue(array('itbz\test\Working')));
        $generator = new RouterGenerator($mustache, $scanner);
    }*/

    public function testGetDependencyContainerCode()
    {
        $mustache = $this->getMock('\Mustache_Engine');
        $template = $this->getMock(
            '\Mustache_Template',
            array(),
            array(),
            '',
            false
        );
        $mustache->expects($this->once())
            ->method('loadTemplate')
            ->with('Dependencies')
            ->will($this->returnValue($template));

        $template->expects($this->once())
            ->method('render')
            ->will($this->returnValue('foobar'));

        $generator = new RouterGenerator($mustache);
        $generator->addClass('itbz\test\Working');

        $this->assertEquals('foobar', $generator->getDependencyContainerCode());
    }

    public function testGetRouteCode()
    {
        $mustache = $this->getMock('\Mustache_Engine');
        $template = $this->getMock(
            '\Mustache_Template',
            array(),
            array(),
            '',
            false
        );
        $mustache->expects($this->once())
            ->method('loadTemplate')
            ->with('routes')
            ->will($this->returnValue($template));

        $template->expects($this->once())
            ->method('render')
            ->will($this->returnValue('foobar'));

        $generator = new RouterGenerator($mustache);
        $generator->setRoot('root');
        $generator->addClass('itbz\test\Working');

        $this->assertEquals('foobar', $generator->getRouteCode());
    }

    public function testGetStaticCode()
    {
        $mustache = $this->getMock('\Mustache_Engine');
        $template = $this->getMock(
            '\Mustache_Template',
            array(),
            array(),
            '',
            false
        );
        $mustache->expects($this->once())
            ->method('loadTemplate')
            ->with('static')
            ->will($this->returnValue($template));

        $template->expects($this->once())
            ->method('render')
            ->will($this->returnValue('foobar'));

        $generator = new RouterGenerator($mustache);
        $generator->setCaller('caller');
        $generator->setContainer('container');

        $this->assertEquals('foobar', $generator->getStaticCode());
    }

    public function testGenerate()
    {
        $stub = $this->getMock(
            'itbz\inroute\RouterGenerator',
            array('getStaticCode', 'getRouteCode', 'getDependencyContainerCode'),
            array(),
            '',
            false
        );

        $stub->expects($this->once())
            ->method('getDependencyContainerCode')
            ->will($this->returnValue('1'));

        $stub->expects($this->once())
            ->method('getRouteCode')
            ->will($this->returnValue('2'));

        $stub->expects($this->once())
            ->method('getStaticCode')
            ->will($this->returnValue('3'));

        $this->assertEquals('123', $stub->generate());
    }
}
