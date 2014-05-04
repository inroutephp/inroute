<?php
namespace inroute\Compiler;

class CodeGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testGenerateCode()
    {
        $factory = $this->getMockBuilder('inroute\Compiler\RouteFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $factory->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator(array())));

        $classIterator = $this->getMockBuilder('hanneskod\classtools\FilterableClassIterator')
            ->disableOriginalConstructor()
            ->getMock();

        $classIterator->expects($this->once())
            ->method('getIterator')
            ->will(
                $this->returnValue(
                    new \ArrayIterator(
                        array(__CLASS__ => new \ReflectionClass(__CLASS__))
                    )
                )
            );

        $generator = new CodeGenerator($factory, $classIterator);

        $this->assertRegExp(
            '/namespace inroute\\\Router;/',
            (string)$generator
        );
    }
}
