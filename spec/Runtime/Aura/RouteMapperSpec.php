<?php

declare(strict_types = 1);

namespace spec\inroutephp\inroute\Runtime\Aura;

use inroutephp\inroute\Runtime\Aura\RouteMapper;
use inroutephp\inroute\Runtime\RouteInterface;
use Aura\Router\Map;
use Aura\Router\Route as AuraRoute;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RouteMapperSpec extends ObjectBehavior
{
    function let(Map $map)
    {
        $this->beConstructedWith($map);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RouteMapper::CLASS);
    }

    function it_maps($map, RouteInterface $route, AuraRoute $auraRoute)
    {
        $route->getName()->willReturn('name');
        $route->getPath()->willReturn('path');
        $route->getHttpMethods()->willReturn(['METHOD']);

        $map->route('name', 'path', $route)->willReturn($auraRoute)->shouldBeCalled();

        $auraRoute->allows(['METHOD'])->willReturn($auraRoute)->shouldBeCalled();

        $this->mapRoute($route);
    }
}
