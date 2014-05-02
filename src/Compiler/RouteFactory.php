<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\Compiler;

use IteratorAggregate;
use inroute\Router\Route;

/**
 * Create Routes from Definition
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class RouteFactory implements IteratorAggregate
{
    private $definitions, $tokenizer;

    /**
     * @param DefinitionFactory $definitions Route definition source
     * @param Tokenizer         $tokenizer   Tokenizer used when parsing paths
     */
    public function __construct(DefinitionFactory $definitions, Tokenizer $tokenizer = null)
    {
        $this->definitions = $definitions;
        $this->tokenizer = $tokenizer ?: new Tokenizer;
    }

    /**
     * @return \Iterator
     * @todo   Implement as a generator
     */
    public function getIterator()
    {
        $routes = array();

        foreach ($this->definitions as $definition) {
            $routes[] = new Route(
                $this->tokenizer->tokenize($definition->read('path')),
                $this->tokenizer->getRegex(),
                $definition->read('httpmethods'),
                $definition->read('controller'),
                $definition->read('controllerMethod'),
                $definition->getPreFilters(),
                $definition->getPostFilters()
            );
        }

        return new \ArrayIterator($routes);
    }
}
