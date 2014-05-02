<?php
namespace inroute\classtools;

class NameFilterIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function testFilter()
    {
        $iterator = new NameFilterIterator('/Class/');
        $iterator->addPath(__DIR__.'/../../src/classtools');

        $result = iterator_to_array($iterator);

        $this->assertArrayNotHasKey(
            'inroute\classtools\NameFilterIterator',
            $result
        );

        $this->assertArrayHasKey(
            'inroute\classtools\ClassMinimizer',
            $result
        );

        $result = iterator_to_array(
            $iterator->filterName('/Iterator/')
        );

        $this->assertArrayNotHasKey(
            'inroute\classtools\ClassMinimizer',
            $result
        );

        $this->assertArrayHasKey(
            'inroute\classtools\ClassIterator',
            $result
        );
    }
}
