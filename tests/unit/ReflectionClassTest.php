<?php
/**
 * This file is part of the inroute package
 *
 * Copyright (c) 2013 Hannes Forsgård
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iio\inroute;

class ReflectionClassTest extends \PHPUnit_Framework_TestCase
{
    public function testIsController()
    {
        $no = new ReflectionClass('unit\data\NoInroute');
        $this->assertFalse($no->isController());

        $yes = new ReflectionClass('unit\data\NoConstructor');
        $this->assertTrue($yes->isController());
    }

    public function testIsCaller()
    {
        $no = new ReflectionClass('unit\data\NoInroute');
        $this->assertFalse($no->isCaller());
    }

    public function testIsContainer()
    {
        $no = new ReflectionClass('unit\data\NoInroute');
        $this->assertFalse($no->isContainer());
    }

    public function testNoConstructor()
    {
        $refl = new ReflectionClass('unit\data\NoConstructor');
        $this->assertEquals(
            array(),
            $refl->getInjections(),
            'List of injections for class with no constructor should be an empty array'
        );
    }

    public function testGetFactoryName()
    {
        $refl = new ReflectionClass('unit\data\NoConstructor');
        $this->assertEquals('unit_data_NoConstructor', $refl->getFactoryName());
    }

    /**
     * @expectedException iio\inroute\Exception\InjectionException
     */
    public function testInjectionMissing()
    {
        $refl = new ReflectionClass('unit\data\InjectionMissing');
        $refl->getInjections();
    }

    /**
     * @expectedException iio\inroute\Exception\InjectionException
     */
    public function testParamTagMissing()
    {
        $refl = new ReflectionClass('unit\data\ParamTagMissing');
        $refl->getInjections();
    }

    /**
     * @expectedException iio\inroute\Exception\InjectionException
     */
    public function testParamMissing()
    {
        $refl = new ReflectionClass('unit\data\InjectedParameterMissing');
        $refl->getInjections();
    }

    public function testGetSignature()
    {
        $refl = new ReflectionClass('unit\data\Working');
        $this->assertEquals('$bar, $x, $y', $refl->getSignature());
    }

    public function testGetInjections()
    {
        $refl = new ReflectionClass('unit\data\Working');
        $expected = array(
            array(
                'name' => '$bar',
                'class' => 'DateTime',
                'array' => false,
                'factory' => 'foobar'
            ),
            array(
                'name' => '$x',
                'class' => '',
                'array' => true,
                'factory' => 'xfactory'
            ),
            array(
                'name' => '$y',
                'class' => '',
                'array' => false,
                'factory' => 'xx'
            )
        );
        $this->assertEquals($expected, $refl->getInjections());
    }

    public function testGetRoutes()
    {
        $refl = new ReflectionClass('unit\data\Working');
        $routes = $refl->getRoutes();

        $this->assertEquals(3, count($routes));

        $this->assertEquals(
            'foo',
            $routes[0]['name']
        );

        $this->assertEquals(
            array('GET'),
            $routes[0]['httpmethod']
        );

        $this->assertEquals(
            '/root/foo/{:name}',
            $routes[0]['path']
        );
    }
}
