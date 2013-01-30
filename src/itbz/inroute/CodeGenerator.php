<?php
/**
 * This file is part of the inroute package
 *
 * Copyright (c) 2013 Hannes Forsgård
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace itbz\inroute;

use itbz\inroute\Exception\RuntimeExpection;
use Mustache_Engine;

/**
 * The Inrout router generator
 *
 * Generates php code that returns a custom Inroute object. Input includes
 * classnames, an optional www-root, a caller classname (if the default caller
 * is none is specified) and a DI-container.
 * 
 * @package inroute
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 */
class CodeGenerator
{
    /**
     * Mustache instance
     *
     * @var Mustache_Engine
     */
    private $mustache;

    /**
     * List of classes to process
     *
     * @var array
     */
    private $reflectionClasses = array();

    /**
     * Root path
     *
     * @var string
     */
    private $root;

    /**
     * Caller classname
     *
     * @var string
     */
    private $caller = 'DefaultCaller';

    /**
     * Container classname
     *
     * @var string
     */
    private $container = 'DefaultContainer';

    /**
     * The Inrout router generator
     *
     * @param Mustache_Engine $mustache
     */
    public function __construct(Mustache_Engine $mustache)
    {
        $this->mustache = $mustache;
    }

    /**
     * Bulk add array of classnames
     *
     * @param array $classes
     *
     * @return CodeGenerator instance for chaining
     */
    public function addClasses(array $classes)
    {
        foreach ($classes as $classname) {
            $this->addClass($classname);
        }

        return $this;
    }

    /**
     * Add class for processing
     *
     * @param string $classname
     *
     * @return CodeGenerator instance for chaining
     */
    public function addClass($classname)
    {
        if (!isset($this->reflectionClasses[$classname])) {
            $reflClass = new ReflectionClass($classname);
            if ($reflClass->isInroute()) {
                $this->reflectionClasses[$classname] = $reflClass;
            }
            if ($reflClass->isContainer()) {
                $this->setContainerClassName('\\'.$reflClass->getName());
            }
            if ($reflClass->isCaller()) {
                $this->setCallerClassName('\\'.$reflClass->getName());
            }
        }

        return $this;
    }

    /**
     * Set root path
     *
     * @param string $root
     *
     * @return CodeGenerator instance for chaining
     */
    public function setRoot($root)
    {
        assert('is_string($root)');
        $this->root = $root;

        return $this;
    }

    /**
     * Set caller class name
     *
     * @param string $classname
     *
     * @return CodeGenerator instance for chaining
     */
    public function setCallerClassName($classname)
    {
        assert('is_string($classname)');
        $this->caller = $classname;

        return $this;
    }

    /**
     * Get name of caller class
     *
     * @return string Name of supplied caller class
     */
    public function getCallerClassName()
    {
        return $this->caller;
    }

    /**
     * Set DI-container class name
     *
     * @param string $classname
     *
     * @return CodeGenerator instance for chaining
     */
    public function setContainerClassName($classname)
    {
        assert('is_string($classname)');
        $this->container = $classname;

        return $this;
    }

    /**
     * Get name of DI-container class
     *
     * @return string Name of supplied DI-container
     */
    public function getContainerClassName()
    {
        return $this->container;
    }

    /**
     * Get code for the generated DIC
     *
     * @return string
     */
    public function getDependencyContainerCode()
    {
        $factories = array();
        foreach ($this->reflectionClasses as $refl) {
            $factories[] = array(
                'name' => $refl->getFactoryName(),
                'class' => $refl->getName(),
                'signature' => $refl->getSignature(),
                'params' => $refl->getInjections()
            );
        }

        return $this->mustache->loadTemplate('Dependencies')
            ->render(array('factories' => $factories));
    }

    /**
     * Get code for the generated route map
     *
     * @return string
     */
    public function getRouteCode()
    {
        $routes = array();
        foreach ($this->reflectionClasses as $refl) {
            foreach ($refl->getRoutes() as $route) {
                $routes[] = array(
                    'name' => $route['name'],
                    'path' => $route['path'],
                    'method' => $route['httpmethod'],
                    'cntrlfactory' => $refl->getFactoryName(),
                    'cntrlmethod' => $route['name']
                );
            }
        }

        return $this->mustache->loadTemplate('routes')
            ->render(array('routes' => $routes, 'root' => $this->root));
    }

    /**
     * Get static bootstrap code
     *
     * @return string
     */
    public function getStaticCode()
    {
        return $this->mustache->loadTemplate('static')
            ->render(
                array(
                    'caller' => $this->getCallerClassName(),
                    'container' => $this->getContainerClassName()
                )
            );
    }

    /**
     * Generate code
     *
     * @return string The generated code
     */
    public function generate()
    {
        return $this->getDependencyContainerCode()
            . $this->getRouteCode()
            . $this->getStaticCode();
    }
}
