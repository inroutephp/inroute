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
 * Negate a filter
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class NotFilter implements FilterInterface
{
    use FilterInterfaceTrait, FilterableTrait;

    private $filter;

    public function __construct(FilterInterface $filter)
    {
        $this->filter = $filter;
    }

    public function getIterator()
    {
        $filtered = iterator_to_array($this->filter->getIterator());
        foreach ($this->getFilterable() as $className => $reflectedClass) {
            if (!isset($filtered[$className])) {
                yield $className => $reflectedClass;
            }
        }
    }
}
