<?php
namespace inroute\Compiler;

class DefinitionFinderOldTest extends \PHPUnit_Framework_TestCase
{
    public function testNonController()
    {
        $this->setExpectedException('inroute\Exception\RuntimeException');
        new DefinitionFinderOld('data\NoInroute');
    }

    public function testExtract()
    {
        $defs = array();

        foreach (new DefinitionFinderOld('data\Working') as $def) {
            $defs[] = $def;
        }

        $expected = array(
            array(
                'controller'       => 'data\Working',
                'controllerMethod' => 'foo',
                'httpmethods'      => array('GET'),
                'path'             => '/root/foo/{:name}'
            ),
            array(
                'controller'       => 'data\Working',
                'controllerMethod' => 'bar',
                'httpmethods'      => array('POST'),
                'path'             => '/root/bar/{:name}'
            ),
            array(
                'controller'       => 'data\Working',
                'controllerMethod' => 'bar',
                'httpmethods'      => array('POST'),
                'path'             => '/root/baar/{:name}'
            )
        );

        $this->assertEquals($expected, $defs);
    }
}
