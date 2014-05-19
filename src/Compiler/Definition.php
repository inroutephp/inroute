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
use inroute\Router\Environment;
use inroute\Exception\CompileTimeException;
use ReflectionClass;
use ReflectionException;

/**
 * Describes an annotated controller method (a route)
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class Definition
{
    /**
     * @var Annotations Class annotations object
     */
    private $classAnnotations;

    /**
     * @var Annotations Method annotations object
     */
    private $methodAnnotations;

    /**
     * @var Environment Route environment
     */
    private $environment;

    /**
     * @var string[] List of pre filter classnames
     */
    private $preFilters = [];

    /**
     * @var string[] List of post filter classnames
     */
    private $postFilters = [];

    /**
     * Constructor
     *
     * @param Annotations $classAnnot  Controller class annotations
     * @param Annotations $methodAnnot Controller method annotations
     * @param Environment $env         Route environment
     */
    public function __construct(Annotations $classAnnot, Annotations $methodAnnot, Environment $env)
    {
        $this->classAnnotations = $classAnnot;
        $this->methodAnnotations = $methodAnnot;
        $this->environment = $env;
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
     * @param  string $classname
     * @return void
     * @throws CompileTimeException If $classname does not reprsent a valid filter
     */
    public function addPreFilter($classname)
    {
        $this->validateFilter($classname, 'inroute\Router\PreFilterInterface');
        $this->preFilters[] = $classname;
    }

    /**
     * Get pre route filters
     *
     * @return string[]
     */
    public function getPreFilters()
    {
        return $this->preFilters;
    }

    /**
     * Add a post route filter
     *
     * @param  string $classname
     * @return void
     * @throws CompileTimeException If $classname does not reprsent a valid filter
     */
    public function addPostFilter($classname)
    {
        $this->validateFilter($classname, 'inroute\Router\PostFilterInterface');
        $this->postFilters[] = $classname;
    }

    /**
     * Get post route filters
     *
     * @return string[]
     */
    public function getPostFilters()
    {
        return $this->postFilters;
    }

    /**
     * Get route environment
     *
     * @return Environment
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Validate that $classname represents a valid $interfaceName filter
     *
     * @param  string $classname
     * @param  string $interfaceName
     * @return void
     * @throws CompileTimeException If $classname does not reprsent a valid filter
     */
    private function validateFilter($classname, $interfaceName)
    {
        try {
            $reflectedClass = new ReflectionClass($classname);
            if (!$reflectedClass->implementsInterface($interfaceName)) {
                throw new CompileTimeException("Filter <$classname> must implement <$interfaceName>");
            }
        } catch (ReflectionException  $e) {
            throw new CompileTimeException($e->getMessage(), 0, $e);
        }
    }
}
