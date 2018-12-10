<?php

declare(strict_types = 1);

namespace inroutephp\inroute\Compiler;

final class Compiler implements CompilerInterface
{
    /**
     * @var CompilerPassInterface[]
     */
    private $passes = [];

    public function addCompilerPass(CompilerPassInterface $pass): void
    {
        $this->passes[] = $pass;
    }

    public function compile(RouteCollectionInterface ...$collections): RouteCollectionInterface
    {
        $routes = [];

        foreach ($collections as $collection) {
            foreach ($collection->getRoutes() as $route) {
                foreach ($this->passes as $pass) {
                    $route = $pass->processRoute($route);
                }
                if ($route->isRoutable()) {
                    $routes[] = $route;
                }
            }
        }

        return new RouteCollection($routes);
    }
}
