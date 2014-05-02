<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\classtools;

use ReflectionClass;

/**
 * Iterate over classes found in filesystem and get ReflectionClass objects
 *
 * Iterator yields classnames as keys and ReflectionClass objects as values
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class ReflectionClassIterator extends ClassIterator
{
    /**
     * Create a new iterator where classes are filtered based on type
     *
     * @param  string $typename
     * @return ReflectionClassIterator
     */
    public function filterType($typename)
    {
        return new TypeFilterIterator($typename, $this);
    }

    /**
     * Create a new iterator where classes are filtered based on name
     *
     * @param  string $pattern Regular expression used when filtering
     * @return ReflectionClassIterator
     */
    public function filterName($pattern)
    {
        return new NameFilterIterator($pattern, $this);
    }

    /**
     * Add class to iterator
     *
     * @param string $classname
     * @param mixed  $content   Not used
     */
    public function addClass($classname, $content = '')
    {
        parent::addClass($classname, new ReflectionClass($classname));
    }
}
