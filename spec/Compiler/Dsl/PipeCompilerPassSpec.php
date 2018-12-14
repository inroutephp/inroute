<?php

declare(strict_types = 1);

namespace spec\inroutephp\inroute\Compiler\Dsl;

use inroutephp\inroute\Compiler\Dsl\PipeCompilerPass;
use inroutephp\inroute\Annotations\Pipe;
use inroutephp\inroute\Compiler\CompilerPassInterface;
use inroutephp\inroute\Runtime\RouteInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PipeCompilerPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(PipeCompilerPass::CLASS);
    }

    function it_is_a_compiler_pass()
    {
        $this->shouldHaveType(CompilerPassInterface::CLASS);
    }

    function it_can_process_routes(RouteInterface $route)
    {
        $annotation = new class {
            public $middlewares = ['middleware'];
            public $attributes = ['key' => 'value'];
        };

        $route->withMiddleware('middleware')->willReturn($route)->shouldBeCalled();
        $route->withAttribute('key', 'value')->willReturn($route)->shouldBeCalled();

        $route->getAnnotations(Pipe::CLASS)->willReturn([$annotation])->shouldBeCalled();

        $this->processRoute($route)->shouldReturn($route);
    }
}
