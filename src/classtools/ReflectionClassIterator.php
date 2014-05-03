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
class ReflectionClassIterator extends ClassIterator implements Filterable
{
    use FilterableTrait;

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
