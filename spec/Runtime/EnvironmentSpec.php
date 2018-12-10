<?php

declare(strict_types = 1);

namespace spec\inroutephp\inroute\Runtime;

use inroutephp\inroute\Runtime\Environment;
use inroutephp\inroute\Runtime\RouteInterface;
use inroutephp\inroute\Runtime\UrlGeneratorInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class EnvironmentSpec extends ObjectBehavior
{
    function let(RouteInterface $route, UrlGeneratorInterface $urlGenerator)
    {
        $this->beConstructedWith($route, $urlGenerator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Environment::CLASS);
    }

    function it_contains_a_route($route)
    {
        $this->getRoute()->shouldReturn($route);
    }

    function it_contains_a_generator($urlGenerator)
    {
        $this->getUrlGenerator()->shouldReturn($urlGenerator);
    }
}
