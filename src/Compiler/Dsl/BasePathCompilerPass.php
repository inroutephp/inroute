<?php

declare(strict_types = 1);

namespace inroutephp\inroute\Compiler\Dsl;

use inroutephp\inroute\Annotations\BasePath;
use inroutephp\inroute\Compiler\CompilerPassInterface;
use inroutephp\inroute\Runtime\RouteInterface;

final class BasePathCompilerPass implements CompilerPassInterface
{
    public function processRoute(RouteInterface $route): RouteInterface
    {
        if ($basePath = $route->getAnnotation(BasePath::CLASS)) {
            $route = $route->withPath(
                $basePath->path . $route->getPath()
            );
        }

        return $route;
    }
}
