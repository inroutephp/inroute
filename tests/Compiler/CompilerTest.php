<?php
namespace inroute\Compiler;

class CompilerTest extends \PHPUnit_Framework_TestCase
{
    public function testCompile()
    {
        $classIterator = $this->getMock('inroute\classtools\ReflectionClassIterator');
        $classIterator->expects($this->any())
            ->method('filterType')
            ->will($this->returnValue($classIterator));
        $classIterator->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator(array())));

        $logger = $this->getMock('Psr\Log\LoggerInterface');

        $compiler = new Compiler($classIterator, $logger);

        $this->assertTrue(!!$compiler->compile());
    }
}
