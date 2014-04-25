<?php
namespace inroute\Compiler;

class ClassMinimizerTest extends \PHPUnit_Framework_TestCase
{
    public function testMinimize()
    {
        $minimizer = new ClassMinimizer('inroute\Compiler\ClassMinimizer');
        $this->assertRegExp(
            '/public function getPhpCode/',
            $minimizer->minimize(),
            'The generated code should include method getPhpCode'
        );
    }
}
