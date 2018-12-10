<?php

namespace inroutephp\inroute\Compiler;

use inroutephp\inroute\Runtime\RouteInterface;

interface RouteCollectionInterface
{
    /**
     * Get loaded routes
     *
     * @return RouteInterface[]
     */
    public function getRoutes(): array;
}
