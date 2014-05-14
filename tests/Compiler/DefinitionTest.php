<?php
namespace inroute\Compiler;

class DefinitionTest extends \PHPUnit_Framework_TestCase
{
    public function testStoredValues()
    {
        $def = new Definition(
            \Mockery::mock('zpt\anno\Annotations'),
            \Mockery::mock('zpt\anno\Annotations')
        );

        $def->write('foo', 'bar');
        $this->assertEquals('bar', $def->read('foo'));

        $this->assertEquals(
            array('foo'=>'bar'),
            $def->toArray()
        );

        $this->setExpectedException('inroute\Exception\LogicException');
        $this->assertFalse($def->exists('does-not-exist'));
        $def->read('does-not-exist');
    }

    public function testFilters()
    {
        $def = new Definition(
            \Mockery::mock('zpt\anno\Annotations'),
            \Mockery::mock('zpt\anno\Annotations')
        );

        $filter = function () {
        };

        $this->assertEmpty($def->getPreFilters());
        $def->addPreFilter($filter);
        $this->assertEquals(array($filter), $def->getPreFilters());

        $this->assertEmpty($def->getPostFilters());
        $def->addPostFilter($filter);
        $this->assertEquals(array($filter), $def->getPostFilters());
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

        $def = new Definition($classAnnotations, $methodAnnotations);

        $this->assertEquals('', $def->getClassAnnotation('does-not-exist'));
        $this->assertEquals('foobar', $def->getClassAnnotation('exist'));

        $this->assertEquals('', $def->getMethodAnnotation('does-not-exist'));
        $this->assertEquals('foobar', $def->getMethodAnnotation('exist'));
    }
}
