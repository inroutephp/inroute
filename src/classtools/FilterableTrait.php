<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\classtools;

use IteratorAggregate;

/**
 * Basic implementation of filterable interface
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
trait FilterableTrait
{
    /**
     * Bind filter to iterator
     *
     * @param  FilterInterface $filter
     * @return FilterInterface The bound filter
     */
    public function filter(FilterInterface $filter)
    {
        $filter->bindFilter($this);
        return $filter;
    }

    /**
     * Create a new iterator where classes are filtered based on type
     *
     * @param  string $typename
     * @return Filterable
     */
    public function filterType($typename)
    {
        return $this->filter(new TypeFilter($typename));
    }

    /**
     * Create a new iterator where classes are filtered based on name
     *
     * @param  string $pattern Regular expression used when filtering
     * @return Filterable
     */
    public function filterName($pattern)
    {
        return $this->filter(new NameFilter($pattern));
    }

    /**
     * Create iterator where classes are filtered based on method return value
     *
     * @param  string  $methodName  Name of ReflectionClass method
     * @param  mixed   $returnValue
     * @return Filterable
     */
    public function where($methodName, $returnValue = true)
    {
        return $this->filter(new WhereFilter($methodName, $returnValue));
    }

    /**
     * Negate a filter
     *
     * @param  FilterInterface $filter
     * @return Filterable
     */
    public function not(FilterInterface $filter)
    {
        return $this->filter(new NotFilter($filter));
    }
}
