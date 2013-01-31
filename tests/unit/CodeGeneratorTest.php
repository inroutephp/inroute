<?php
namespace iio\inroute;

class CodeGeneratorTest extends \PHPUnit_Framework_TestCase
{
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

        $generator = new CodeGenerator($mustache);
        $generator->addClass('unit\data\Working');

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

        $generator = new CodeGenerator($mustache);
        $generator->setRoot('root');
        $generator->addClass('unit\data\Working');

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

        $generator = new CodeGenerator($mustache);
        $generator->setContainerClassName('classname');

        $this->assertEquals('foobar', $generator->getStaticCode());
    }

    public function testGenerate()
    {
        $stub = $this->getMock(
            'iio\inroute\CodeGenerator',
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

    public function testAddClasses()
    {
        $stub = $this->getMock(
            'iio\inroute\CodeGenerator',
            array('addClass'),
            array(),
            '',
            false
        );

        $stub->expects($this->once())
            ->method('addClass')
            ->with('classname');

        $stub->addClasses(array('classname'));
    }

    public function testAddContainerClass()
    {
        $mustache = $this->getMock('\Mustache_Engine');
        $generator = new CodeGenerator($mustache);
        $generator->addClass('\unit\data\Container');
        $this->assertEquals(
            '\unit\data\Container',
            $generator->getContainerClassName()
        );
    }

    public function testAddCallerClass()
    {
        $mustache = $this->getMock('\Mustache_Engine');
        $generator = new CodeGenerator($mustache);
        $generator->addClass('\iio\inroute\DefaultCaller');
        $this->assertEquals(
            '\iio\inroute\DefaultCaller',
            $generator->getCallerClassName()
        );
    }
}
