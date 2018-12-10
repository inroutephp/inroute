<?php

declare(strict_types = 1);

namespace spec\inroutephp\inroute\Compiler;

use inroutephp\inroute\Compiler\Compiler;
use inroutephp\inroute\Compiler\CompilerInterface;
use inroutephp\inroute\Compiler\CompilerPassInterface;
use inroutephp\inroute\Compiler\RouteCollectionInterface;
use inroutephp\inroute\Compiler\RouteCollection;
use inroutephp\inroute\Runtime\RouteInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CompilerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Compiler::CLASS);
    }

    function it_is_a_compiler()
    {
        $this->shouldHaveType(CompilerInterface::CLASS);
    }

    function it_can_compile(CompilerPassInterface $pass, RouteCollectionInterface $routes, RouteInterface $route)
    {
        $route->isRoutable()->willReturn(true);

        $routes->getRoutes()->willReturn([$route]);

        $pass->processRoute($route)->willReturn($route)->shouldBeCalled();

        $this->addCompilerPass($pass);

        $this->compile($routes)->shouldBeLike(new RouteCollection([$route->getWrappedObject()]));
    }

    function it_ignores_non_routables(
        CompilerPassInterface $pass,
        RouteCollectionInterface $routes,
        RouteInterface $route
    ) {
        $route->isRoutable()->willReturn(false);

        $routes->getRoutes()->willReturn([$route]);
        $pass->processRoute($route)->willReturn($route);
        $this->addCompilerPass($pass);

        $this->compile($routes)->shouldBeLike(new RouteCollection([]));
    }
}
