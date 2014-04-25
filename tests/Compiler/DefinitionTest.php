<?php
namespace inroute\Compiler;

class DefinitionTest extends \PHPUnit_Framework_TestCase
{
    public function testStoredValues()
    {
        $def = new Definition(
            $this->getMock('zpt\anno\Annotations'),
            $this->getMock('zpt\anno\Annotations')
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
            $this->getMock('zpt\anno\Annotations'),
            $this->getMock('zpt\anno\Annotations')
        );

        $filter = function(){};

        $this->assertEmpty($def->getPreFilters());        
        $def->addPreFilter($filter);
        $this->assertEquals(array($filter), $def->getPreFilters());

        $this->assertEmpty($def->getPostFilters());        
        $def->addPostFilter($filter);
        $this->assertEquals(array($filter), $def->getPostFilters());
    }

    public function testAnnotations()
    {
        $classAnnotations = $this->getMock('zpt\anno\Annotations');

        $classAnnotations->expects($this->at(0))
            ->method('hasAnnotation')
            ->with('does-not-exist')
            ->will($this->returnValue(false));

        $classAnnotations->expects($this->at(1))
            ->method('hasAnnotation')
            ->with('exist')
            ->will($this->returnValue(true));

        $classAnnotations->expects($this->once())
            ->method('offsetGet')
            ->with('exist')
            ->will($this->returnValue('foobar'));

        $methodAnnotations = $this->getMock('zpt\anno\Annotations');

        $methodAnnotations->expects($this->at(0))
            ->method('hasAnnotation')
            ->with('does-not-exist')
            ->will($this->returnValue(false));

        $methodAnnotations->expects($this->at(1))
            ->method('hasAnnotation')
            ->with('exist')
            ->will($this->returnValue(true));

        $methodAnnotations->expects($this->once())
            ->method('offsetGet')
            ->with('exist')
            ->will($this->returnValue('foobar'));

        $def = new Definition($classAnnotations, $methodAnnotations);

        $this->assertEquals('', $def->getClassAnnotation('does-not-exist'));
        $this->assertEquals('foobar', $def->getClassAnnotation('exist'));

        $this->assertEquals('', $def->getMethodAnnotation('does-not-exist'));
        $this->assertEquals('foobar', $def->getMethodAnnotation('exist'));
    }
}
