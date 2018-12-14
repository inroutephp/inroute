<?php

namespace inroutephp\inroute\Compiler;

use inroutephp\inroute\Compiler\RouteCollectionInterface;

interface RouteFactoryInterface
{
    public function createRoutesFrom(string $classname): RouteCollectionInterface;
}
