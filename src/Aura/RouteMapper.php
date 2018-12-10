<?php

declare(strict_types = 1);

namespace inroutephp\inroute\Aura;

use inroutephp\inroute\Runtime\RouteInterface;
use Aura\Router\Map;

final class RouteMapper
{
    /**
     * @var Map
     */
    private $map;

    public function __construct(Map $map)
    {
        $this->map = $map;
    }

    public function mapRoute(RouteInterface $route): void
    {
        $this->map
            ->route($route->getName(), $route->getPath(), $route)
            ->allows($route->getHttpMethods())
            ->tokens($route->getPathTokens())
            ->defaults($route->getPathDefaults())
            ->extras($route->getAttributes())
        ;
    }
}
