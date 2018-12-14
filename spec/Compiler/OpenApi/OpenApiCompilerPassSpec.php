<?php

declare(strict_types = 1);

namespace spec\inroutephp\inroute\Compiler\OpenApi;

use inroutephp\inroute\Compiler\OpenApi\OpenApiCompilerPass;
use inroutephp\inroute\Compiler\CompilerPassInterface;
use inroutephp\inroute\Runtime\RouteInterface;
use OpenApi\Annotations\Operation;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class OpenApiCompilerPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(OpenApiCompilerPass::CLASS);
    }

    function it_is_a_compiler_pass()
    {
        $this->shouldHaveType(CompilerPassInterface::CLASS);
    }

    function it_can_process_routes(RouteInterface $route)
    {
        $operation = new class {
            public $method = 'method';
            public $path = 'path';
            public $operationId = 'name';
        };

        $route->withRoutable(true)->willReturn($route)->shouldBeCalled();
        $route->withHttpMethod('method')->willReturn($route)->shouldBeCalled();
        $route->withPath('path')->willReturn($route)->shouldBeCalled();
        $route->withName('name')->willReturn($route)->shouldBeCalled();

        $route->getAnnotation(Operation::CLASS)->willReturn($operation)->shouldBeCalled();

        $this->processRoute($route)->shouldReturn($route);
    }
}
