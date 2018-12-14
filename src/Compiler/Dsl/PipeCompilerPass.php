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
        foreach ($route->getAnnotations(Pipe::CLASS) as $pipe) {
            foreach ((array)$pipe->middlewares as $middleware) {
                $route = $route->withMiddleware($middleware);
            }

            foreach ((array)$pipe->attributes as $key => $value) {
                $route = $route->withAttribute($key, $value);
            }
        }

        return $route;
    }
}
