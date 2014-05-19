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
use ReflectionClass;
use zpt\anno\Annotations;
use inroute\Router\Environment;

/**
 * Iterate over route descriptions found in one controller class
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class DefinitionIterator implements IteratorAggregate
{
    /**
     * @var ReflectionClass Controller class
     */
    private $class;

    /**
     * Constructor
     *
     * @param ReflectionClass $class Reflected controller class
     */
    public function __construct(ReflectionClass $class)
    {
        $this->class = $class;
    }

    /**
     * Implementation of IteratorAggregate
     *
     * @return \Iterator
     */
    public function getIterator()
    {
        $classAnnotations = new Annotations($this->class);

        /** @var \ReflectionMethod $method */
        foreach ($this->class->getMethods() as $method) {
            if ($method->isConstructor()) {
                continue;
            }

            $definition = new Definition(
                $classAnnotations,
                new Annotations($method),
                new Environment(
                    [
                        'controller_name' => $this->class->getName(),
                        'controller_method' => $method->getName()
                    ]
                )
            );

            yield $definition;
        }
    }
}
