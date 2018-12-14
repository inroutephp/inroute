<?php

declare(strict_types = 1);

namespace spec\inroutephp\inroute\Compiler\Dsl;

use inroutephp\inroute\Compiler\Dsl\BasePathCompilerPass;
use inroutephp\inroute\Annotations\BasePath;
use inroutephp\inroute\Compiler\CompilerPassInterface;
use inroutephp\inroute\Runtime\RouteInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BasePathCompilerPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(BasePathCompilerPass::CLASS);
    }

    function it_is_a_compiler_pass()
    {
        $this->shouldHaveType(CompilerPassInterface::CLASS);
    }

    function it_can_process_routes(RouteInterface $route)
    {
        $annotation = new class {
            public $path = '/base';
        };

        $route->getPath()->willReturn('/path');
        $route->withPath('/base/path')->willReturn($route)->shouldBeCalled();

        $route->getAnnotation(BasePath::CLASS)->willReturn($annotation)->shouldBeCalled();

        $this->processRoute($route)->shouldReturn($route);
    }
}
