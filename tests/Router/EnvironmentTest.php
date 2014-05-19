<?php
namespace inroute\Router;

class EnvironmentTest extends \PHPUnit_Framework_TestCase
{
    public function testSetValueInConstructor()
    {
        $env = new Environment(['NameOfKey' => 'value']);

        $this->assertEquals(
            'value',
            $env->get('NameOfKey'),
            'Values passed to constructor must be stored'
        );

        $this->assertEquals(
            'value',
            $env->get('NaMeOfKeY'),
            'Accessing values passed to constructor must be case insensitive'
        );
    }

    public function testSetValueUsingSetMethod()
    {
        $env = new Environment;

        $env->set('SetUsingSet', 'bar');

        $this->assertEquals(
            'bar',
            $env->get('SetUsingSet'),
            'Values passed to set method must be stored'
        );

        $this->assertEquals(
            'bar',
            $env->get('setusingSET'),
            'Accessing values passed to set method must be case insensitive'
        );
    }

    public function testAccessUnsetValue()
    {
        $env = new Environment;

        $this->assertEquals(
            '',
            $env->get('does-not-exist'),
            'Accessing values not set method return empty string'
        );
    }

    public function testConvertEnvironmentToArray()
    {
        $env = new Environment(['NameOfKey' => 'value']);

        $this->assertEquals(
            ['nameofkey' => 'value'],
            $env->toArray(),
            'toArray must return array with lower case keys'
        );
    }
}
