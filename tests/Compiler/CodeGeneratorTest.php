<?php
namespace inroute\Compiler;

class CodeGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testGenerateCode()
    {
        $factory = \Mockery::mock('inroute\Compiler\RouteFactory');
        $factory->shouldReceive('getIterator')->once()->andReturn(new \ArrayIterator(array()));

        $classIterator = \Mockery::mock('hanneskod\classtools\FilterableClassIterator');
        $classIterator->shouldReceive('getIterator')->once()->andReturn(
            new \ArrayIterator(
                array(__CLASS__ => new \ReflectionClass(__CLASS__))
            )
        );

        $generator = new CodeGenerator($factory, $classIterator);

        $this->assertRegExp(
            '/namespace inroute\\\Router;/',
            (string)$generator
        );
    }
}
