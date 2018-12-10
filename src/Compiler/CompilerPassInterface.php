<?php

namespace inroutephp\inroute\Compiler;

use inroutephp\inroute\Runtime\RouteInterface;

interface CompilerPassInterface
{
    public function processRoute(RouteInterface $route): RouteInterface;
}
