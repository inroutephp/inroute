<?php

declare(strict_types = 1);

namespace spec\inroutephp\inroute\Compiler;

use inroutephp\inroute\Compiler\RouteCollection;
use inroutephp\inroute\Compiler\RouteCollectionInterface;
use inroutephp\inroute\Runtime\RouteInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RouteCollectionSpec extends ObjectBehavior
{
    function let(RouteInterface $route)
    {
        $this->beConstructedWith([$route]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RouteCollection::CLASS);
    }

    function it_is_a_collection()
    {
        $this->shouldHaveType(RouteCollectionInterface::CLASS);
    }

    function it_contain_routes($route)
    {
        $this->getRoutes()->shouldReturn([$route]);
    }
}
