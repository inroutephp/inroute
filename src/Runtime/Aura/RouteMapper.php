<?php

declare(strict_types = 1);

namespace inroutephp\inroute\Runtime\Aura;

use inroutephp\inroute\Runtime\RouteInterface;
use Aura\Router\Map;
use Aura\Router\Route;

final class RouteMapper
{
    /** @var Map<Route> */
    private $map;

    /** @param Map<Route> $map */
    public function __construct(Map $map)
    {
        $this->map = $map;
    }

    public function mapRoute(RouteInterface $route): void
    {
        $this->map
            ->route($route->getName(), $route->getPath(), $route)
            ->allows($route->getHttpMethods())
        ;
    }
}
