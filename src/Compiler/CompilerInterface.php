<?php

namespace inroutephp\inroute\Compiler;

interface CompilerInterface
{
    /**
     * Add a compiler pass to process routes
     */
    public function addCompilerPass(CompilerPassInterface $pass): void;

    /**
     * Compile collectioins of routes
     */
    public function compile(RouteCollectionInterface ...$collections): RouteCollectionInterface;
}
