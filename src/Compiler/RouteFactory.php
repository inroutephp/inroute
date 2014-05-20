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
use inroute\Runtime\Route;

/**
 * Create Routes from Definition
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class RouteFactory implements IteratorAggregate
{
    /**
     * @var DefinitionFactory Route definitions iterator
     */
    private $definitions;

    /**
     * @var PathTokenizer Path tokenizer
     */
    private $tokenizer;

    /**
     * Constructor
     *
     * @param DefinitionFactory $definitions Route definition source
     * @param PathTokenizer     $tokenizer   Tokenizer used when parsing paths
     */
    public function __construct(DefinitionFactory $definitions, PathTokenizer $tokenizer)
    {
        $this->definitions = $definitions;
        $this->tokenizer = $tokenizer;
    }

    /**
     * Implementation of IteratorAggregate
     *
     * @return \Iterator
     */
    public function getIterator()
    {
        /** @var Definition $def */
        foreach ($this->definitions as $def) {
            yield new Route(
                $this->tokenizer->tokenize($def->getEnvironment()->get('path')),
                $this->tokenizer->getRegex(),
                $def->getEnvironment(),
                $def->getPreFilters(),
                $def->getPostFilters()
            );
        }
    }
}
