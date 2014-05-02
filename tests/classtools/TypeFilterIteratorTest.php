<?php
namespace inroute\classtools;

class TypeFilterIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function testFilter()
    {
        $iterator = new TypeFilterIterator('IteratorAggregate');
        $iterator->addPath(__DIR__.'/../../src/classtools');

        $result = iterator_to_array($iterator);

        $this->assertArrayNotHasKey(
            'inroute\classtools\ClassMinimizer',
            $result
        );

        $this->assertArrayHasKey(
            'inroute\classtools\ClassIterator',
            $result
        );

        $result = iterator_to_array(
            $iterator->filterType('inroute\classtools\ReflectionClassIterator')
        );

        $this->assertArrayNotHasKey(
            'inroute\classtools\ClassIterator',
            $result
        );

        $this->assertArrayHasKey(
            'inroute\classtools\ReflectionClassIterator',
            $result
        );
    }

    public function testInvalidType()
    {
        $iterator = new TypeFilterIterator('ThisClassDoesNotExist');
        $iterator->addPath(__DIR__.'/../../src/classtools');

        $this->assertEmpty(iterator_to_array($iterator));
    }
}
