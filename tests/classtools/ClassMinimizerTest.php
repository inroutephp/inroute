<?php
namespace inroute\classtools;

class ClassMinimizerTest extends \PHPUnit_Framework_TestCase
{
    public function testMinimize()
    {
        $minimizer = new ClassMinimizer(
            new \ReflectionClass('inroute\classtools\ClassMinimizer')
        );
        $this->assertRegExp(
            '/public function getPhpCode/',
            $minimizer->minimize(),
            'The generated code should include method getPhpCode'
        );
    }
}
