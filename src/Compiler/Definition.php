<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\Compiler;

use zpt\anno\Annotations;
use Closure;
use inroute\Exception\LogicException;

/**
 * Describes an annotated controller method (a route)
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class Definition
{
    private $classAnnotations;
    private $methodAnnotations;
    private $data = array();
    private $preFilters = array();
    private $postFilters = array();

    /**
     * Constructor
     *
     * @param Annotations $classAnnotations  Controller class annotations
     * @param Annotations $methodAnnotations Controller method annotations
     */
    public function __construct(Annotations $classAnnotations, Annotations $methodAnnotations)
    {
        $this->classAnnotations = $classAnnotations;
        $this->methodAnnotations = $methodAnnotations;
    }

    /**
     * Check if controller class contains annotation
     *
     * @param  string  $annotation
     * @return boolean
     */
    public function hasClassAnnotation($annotation)
    {
        return $this->classAnnotations->hasAnnotation($annotation);
    }

    /**
     * Read controller class annotation
     *
     * @param  string $annotation
     * @return mixed  Empty string if annotation does not exist
     */
    public function getClassAnnotation($annotation)
    {
        if (!$this->hasClassAnnotation($annotation)) {
            return '';
        }
        return $this->classAnnotations->offsetGet($annotation);
    }

    /**
     * Check if controller method contains annotation
     *
     * @param  string  $annotation
     * @return boolean
     */
    public function hasMethodAnnotation($annotation)
    {
        return $this->methodAnnotations->hasAnnotation($annotation);
    }

    /**
     * Read controller method annotation
     *
     * @param  string $annotation
     * @return mixed  Empty string if annotation does not exist
     */
    public function getMethodAnnotation($annotation)
    {
        if (!$this->hasMethodAnnotation($annotation)) {
            return '';
        }
        return $this->methodAnnotations->offsetGet($annotation);
    }

    /**
     * Add a pre route filter
     *
     * @param  Closure $filter
     * @return void
     */
    public function addPreFilter(Closure $filter)
    {
        $this->preFilters[] = $filter;
    }

    /**
     * Get pre route filters
     *
     * @return Closure[]
     */
    public function getPreFilters()
    {
        return $this->preFilters;
    }

    /**
     * Add a post route filter
     *
     * @param  Closure $filter
     * @return void
     */
    public function addPostFilter(Closure $filter)
    {
        $this->postFilters[] = $filter;
    }

    /**
     * Get post route filters
     *
     * @return Closure[]
     */
    public function getPostFilters()
    {
        return $this->postFilters;
    }

    /**
     * Store definition data
     *
     * @param  string $key
     * @param  mixed  $value
     * @return void
     */
    public function write($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Check if value is stored
     *
     * @param  string $key
     * @return bool
     */
    public function exists($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * Read definition data
     *
     * @param  string         $key
     * @return mixed          Empty string id data is not set
     * @throws LogicException If key is not definied
     */
    public function read($key)
    {
        if ($this->exists($key)) {
            return $this->data[$key];
        }
        throw new LogicException("Trying to read undefinied value <$key> from route definition.");
    }

    /**
     * Get stored definition data
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }
}
