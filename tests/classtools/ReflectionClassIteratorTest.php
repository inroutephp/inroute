<?php
namespace inroute\classtools;

class ReflectionClassIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function testScanFile()
    {
        $result = iterator_to_array(new ReflectionClassIterator(__FILE__));

        $this->assertEquals(
            new \ReflectionClass(__CLASS__),
            $result[__CLASS__]
        );
    }

    public function testFilterType()
    {
        $it = new ReflectionClassIterator(__DIR__.'/../../src/classtools');

        $result = iterator_to_array(
            $it->filterType('IteratorAggregate')
        );

        $this->assertArrayNotHasKey(
            'inroute\classtools\ClassMinimizer',
            $result,
            'ClassMinimizer does not implement IteratorAggregate'
        );
        $this->assertArrayHasKey(
            'inroute\classtools\ClassIterator',
            $result,
            'ClassIterator does implement IteratorAggregate'
        );

        $result = iterator_to_array(
            $it->filterType('IteratorAggregate')->filterType('inroute\classtools\ReflectionClassIterator')
        );

        $this->assertArrayNotHasKey(
            'inroute\classtools\ClassIterator',
            $result,
            'ClassIterator does not extend ReflectionClassIterator'
        );
        $this->assertArrayHasKey(
            'inroute\classtools\ReflectionClassIterator',
            $result,
            'ReflectionClassIterator is ReflectionClassIterator'
        );
    }

    public function testFilterName()
    {
        $it = new ReflectionClassIterator(__DIR__.'/../../src/classtools');

        $result = iterator_to_array(
            $it->filterName('/Class/')
        );

        $this->assertArrayNotHasKey(
            'inroute\classtools\NameFilterIterator',
            $result
        );
        $this->assertArrayHasKey(
            'inroute\classtools\ClassMinimizer',
            $result
        );

        $result = iterator_to_array(
            $it->filterName('/Class/')->filterName('/Iterator/')
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

    public function testWhereFilter()
    {
        $it = new ReflectionClassIterator(__DIR__.'/../../src/classtools');

        $result = iterator_to_array(
            $it->where('isInterface')
        );

        $this->assertArrayNotHasKey(
            'inroute\classtools\NameFilter',
            $result,
            'NameFilter is not an interface'
        );
        $this->assertArrayHasKey(
            'inroute\classtools\FilterInterface',
            $result,
            'FilterInterface is an interface'
        );
    }

    public function testNotFilter()
    {
        $it = new ReflectionClassIterator(__DIR__.'/../../src/classtools');

        $result = iterator_to_array($it->not($it->where('isInterface')));

        $this->assertArrayHasKey(
            'inroute\classtools\NameFilter',
            $result,
            'NameFilter is not an interface (and thus included using the not filter)'
        );
        $this->assertArrayNotHasKey(
            'inroute\classtools\FilterInterface',
            $result,
            'FilterInterface is an interface (and thus not included using the not filter)'
        );
    }
}
