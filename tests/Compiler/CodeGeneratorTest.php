<?php
namespace inroute\Compiler;

class CodeGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testGenerate()
    {
        $factory = $this->getMockBuilder('inroute\Compiler\RouteFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $factory->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator(array())));

        $classIterator = $this->getMockBuilder('inroute\Compiler\ClassIterator')
            ->disableOriginalConstructor()
            ->getMock();

        $classIterator->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator(array(__CLASS__))));

        $generator = new CodeGenerator($factory, $classIterator);

        $this->assertRegExp(
            '/namespace inroute\\\Router;/',
            (string)$generator
        );
    }
}
