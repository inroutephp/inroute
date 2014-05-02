<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\classtools;

/**
 * Iterate over classes that implements a speficfied interface
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class InterfaceIterator implements ReflectionClassIteratorInterface
{
    private $interface, $classes;

    /**
     * @param string $interface
     * @param ReflectionClassIteratorInterface $classes
     */
    public function __construct($interface, ReflectionClassIteratorInterface $classes)
    {
        $this->interface = $interface;
        $this->classes = $classes;
    }

    /**
     * @return \Iterator
     * @todo   Implement as a generator
     */
    public function getIterator()
    {
        $controllers = array();

        foreach ($this->classes as $className => $reflectedClass) {
            if ($reflectedClass->implementsInterface($this->interface)) {
                $controllers[$className] = $reflectedClass;
            }
        }

        return new \ArrayIterator($controllers);
    }
}
