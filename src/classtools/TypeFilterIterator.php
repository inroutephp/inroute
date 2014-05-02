<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\classtools;

use ReflectionException;
use ArrayIterator;

/**
 * Filter classes of a spefcified type
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class TypeFilterIterator extends ReflectionClassIterator
{
    private $typename, $iterator;

    public function __construct($typename, ReflectionClassIterator $iterator = null)
    {
        $this->typename = $typename;
        $this->iterator = $iterator ?: new ReflectionClassIterator;
    }

    public function addPath($path)
    {
        return $this->iterator->addPath($path);
    }

    public function getIterator()
    {
        // TODO implement as a generator

        $controllers = array();

        foreach ($this->iterator as $className => $reflectedClass) {
            try {
                if ($reflectedClass->implementsInterface($this->typename)) {
                    $controllers[$className] = $reflectedClass;
                }
            } catch (ReflectionException $e) {
                try {
                    if ($reflectedClass->isSubclassOf($this->typename) || $reflectedClass->getName() == $this->typename) {
                        $controllers[$className] = $reflectedClass;
                    }
                } catch (ReflectionException $e) {
                    // Nope
                }
            }
        }

        return new ArrayIterator($controllers);
    }
}
