<?php

declare(strict_types = 1);

namespace inroutephp\inroute\Compiler;

use inroutephp\inroute\Runtime\RouteInterface;

final class RouteCollection implements RouteCollectionInterface
{
    /**
     * @var RouteInterface[]
     */
    private $routes;

    /**
     * @param RouteInterface[] $routes
     */
    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }
}
