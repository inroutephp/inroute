<?php

declare(strict_types = 1);

namespace inroutephp\inroute\Compiler\Dsl;

use inroutephp\inroute\Annotations\Route;
use inroutephp\inroute\Compiler\CompilerPassInterface;
use inroutephp\inroute\Runtime\RouteInterface;

final class RouteCompilerPass implements CompilerPassInterface
{
    public function processRoute(RouteInterface $route): RouteInterface
    {
        if ($annotation = $route->getAnnotation(Route::CLASS)) {
            $route = $route
                ->withRoutable(true)
                ->withHttpMethod($annotation->method)
                ->withPath($annotation->path);

            if ($annotation->name) {
                $route = $route->withName($annotation->name);
            }

            if ($annotation->attributes) {
                foreach ((array)$annotation->attributes as $key => $value) {
                    $route = $route->withAttribute($key, $value);
                }
            }
        }

        return $route;
    }
}
