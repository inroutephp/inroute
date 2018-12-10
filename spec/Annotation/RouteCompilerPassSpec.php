<?php

declare(strict_types = 1);

namespace spec\inroutephp\inroute\Annotation;

use inroutephp\inroute\Annotation\RouteCompilerPass;
use inroutephp\inroute\Annotation\Route;
use inroutephp\inroute\Compiler\CompilerPassInterface;
use inroutephp\inroute\Runtime\RouteInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RouteCompilerPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(RouteCompilerPass::CLASS);
    }

    function it_is_a_compiler_pass()
    {
        $this->shouldHaveType(CompilerPassInterface::CLASS);
    }

    function it_can_process_routes(RouteInterface $route)
    {
        $annotation = new class {
            public $method = 'method';
            public $path = 'path';
            public $name = 'name';
            public $attributes = ['key' => 'value'];
        };

        $route->withRoutable(true)->willReturn($route)->shouldBeCalled();
        $route->withHttpMethod('method')->willReturn($route)->shouldBeCalled();
        $route->withPath('path')->willReturn($route)->shouldBeCalled();
        $route->withName('name')->willReturn($route)->shouldBeCalled();
        $route->withAttribute('key', 'value')->willReturn($route)->shouldBeCalled();

        $route->getAnnotation(Route::CLASS)->willReturn($annotation)->shouldBeCalled();

        $this->processRoute($route)->shouldReturn($route);
    }
}
