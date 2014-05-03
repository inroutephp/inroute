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
 * Filter classes based on name
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class NameFilter implements FilterInterface
{
    use FilterInterfaceTrait, FilterableTrait;

    private $pattern;

    public function __construct($pattern)
    {
        $this->pattern = $pattern;
    }

    public function getIterator()
    {
        foreach ($this->getFilterable() as $className => $reflectedClass) {
            if (preg_match($this->pattern, $className)) {
                yield $className => $reflectedClass;
            }
        }
    }
}
