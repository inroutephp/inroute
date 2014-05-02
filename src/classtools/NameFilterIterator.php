<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\classtools;

use ArrayIterator;

/**
 * Filter classes based on name
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class NameFilterIterator extends ReflectionClassIterator
{
    private $pattern, $iterator;

    public function __construct($pattern, ReflectionClassIterator $iterator = null)
    {
        $this->pattern = $pattern;
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
            if (preg_match($this->pattern, $className)) {
                $controllers[$className] = $reflectedClass;
            }
        }

        return new ArrayIterator($controllers);
    }
}
