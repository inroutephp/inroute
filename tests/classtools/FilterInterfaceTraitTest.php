<?php
namespace inroute\classtools;

class FilterInterfaceTraitTest extends \PHPUnit_Framework_TestCase
{
    public function testFilterableNotSetException()
    {
        $filter = new TypeFilter('');

        $this->setExpectedException('inroute\Exception\LogicException');
        $filter->getFilterable();
    }
}
