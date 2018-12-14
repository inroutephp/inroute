<?php

declare(strict_types = 1);

namespace inroutephp\inroute\Compiler\Dsl;

use inroutephp\inroute\Annotations\Pipe;
use inroutephp\inroute\Compiler\CompilerPassInterface;
use inroutephp\inroute\Runtime\RouteInterface;

final class PipeCompilerPass implements CompilerPassInterface
{
    public function processRoute(RouteInterface $route): RouteInterface
    {
        if ($annotation = $route->getAnnotation(Pipe::CLASS)) {
            foreach ((array)$annotation->middlewares as $middleware) {
                $route = $route->withMiddleware($middleware);
            }

            foreach ((array)$annotation->attributes as $key => $value) {
                $route = $route->withAttribute($key, $value);
            }
        }

        return $route;
    }
}
