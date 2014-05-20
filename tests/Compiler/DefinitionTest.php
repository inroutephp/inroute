<?php
namespace inroute\Compiler;

class DefinitionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetEnvironment()
    {
        $env = \Mockery::mock('inroute\Runtime\Environment');

        $def = new Definition(
            \Mockery::mock('zpt\anno\Annotations'),
            \Mockery::mock('zpt\anno\Annotations'),
            $env
        );

        $this->assertSame($env, $def->getEnvironment());
    }

    public function testFilters()
    {
        $def = new Definition(
            \Mockery::mock('zpt\anno\Annotations'),
            \Mockery::mock('zpt\anno\Annotations'),
            \Mockery::mock('inroute\Runtime\Environment')
        );

        $this->assertEmpty($def->getPreFilters());
        $preFilter = '\inroute\Runtime\PreFilterInterface';
        $def->addPreFilter($preFilter);
        $this->assertEquals([$preFilter], $def->getPreFilters());

        $this->assertEmpty($def->getPostFilters());
        $postFilter = '\inroute\Runtime\PostFilterInterface';
        $def->addPostFilter($postFilter);
        $this->assertEquals([$postFilter], $def->getPostFilters());
    }

    public function testFilterUnvalidIfNotClass()
    {
        $def = new Definition(
            \Mockery::mock('zpt\anno\Annotations'),
            \Mockery::mock('zpt\anno\Annotations'),
            \Mockery::mock('inroute\Runtime\Environment')
        );

        $this->setExpectedException('inroute\Exception\CompilerException');
        $def->addPreFilter('not-a-valid-class');
    }

    public function testFilterUnvalidIfInterfaceNotImplemented()
    {
        $def = new Definition(
            \Mockery::mock('zpt\anno\Annotations'),
            \Mockery::mock('zpt\anno\Annotations'),
            \Mockery::mock('inroute\Runtime\Environment')
        );

        $this->setExpectedException('inroute\Exception\CompilerException');
        $def->addPostFilter('Exception');
    }

    public function testAnnotations()
    {
        $classAnnotations = \Mockery::mock('zpt\anno\Annotations');
        $classAnnotations->shouldReceive('hasAnnotation')->with('does-not-exist')->once()->andReturn(false);
        $classAnnotations->shouldReceive('hasAnnotation')->with('exist')->once()->andReturn(true);
        $classAnnotations->shouldReceive('offsetGet')->with('exist')->once()->andReturn('foobar');

        $methodAnnotations = \Mockery::mock('zpt\anno\Annotations');
        $methodAnnotations->shouldReceive('hasAnnotation')->with('does-not-exist')->once()->andReturn(false);
        $methodAnnotations->shouldReceive('hasAnnotation')->with('exist')->once()->andReturn(true);
        $methodAnnotations->shouldReceive('offsetGet')->with('exist')->once()->andReturn('foobar');

        $def = new Definition(
            $classAnnotations,
            $methodAnnotations,
            \Mockery::mock('inroute\Runtime\Environment')
        );

        $this->assertEquals('', $def->getClassAnnotation('does-not-exist'));
        $this->assertEquals('foobar', $def->getClassAnnotation('exist'));

        $this->assertEquals('', $def->getMethodAnnotation('does-not-exist'));
        $this->assertEquals('foobar', $def->getMethodAnnotation('exist'));
    }
}
