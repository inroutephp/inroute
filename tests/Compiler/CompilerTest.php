<?php
namespace inroute\Compiler;

class CompilerTest extends \PHPUnit_Framework_TestCase
{
    public function testCompile()
    {
        $classIterator = \Mockery::mock('hanneskod\classtools\FilterableClassIterator');
        $classIterator->shouldReceive('filterType->where')->andReturn(new \ArrayIterator(array()));

        $logger = \Mockery::mock('Psr\Log\LoggerInterface');
        $logger->shouldReceive('info')->zeroOrMoreTimes();

        $compiler = new Compiler($classIterator, $logger);

        $this->assertTrue(!!$compiler->compile());
    }
}
