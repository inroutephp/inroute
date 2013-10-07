<?php
/**
 * This file is part of the inroute package
 *
 * Copyright (c) 2013 Hannes Forsgård
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iio\inroute;

use iio\inroute\Exception\RuntimeExpection;
use Mustache_Engine;

/**
 * The Inrout router generator
 *
 * Generates php code that returns a custom Inroute object. Input includes
 * controller classnames and optional www-root, caller and DI-container classnames.
 * 
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class CodeGenerator
{
    /**
     * @var Mustache_Engine Mustache instance
     */
    private $mustache;

    /**
     * @var array List of controller classes to process
     */
    private $controllerClasses = array();

    /**
     * @var string Root path
     */
    private $root;

    /**
     * @var string Caller classname
     */
    private $caller = 'DefaultCaller';

    /**
     * @var string Container classname
     */
    private $container = 'DefaultContainer';

    /**
     * Constructor
     *
     * @param Mustache_Engine $mustache
     */
    public function __construct(Mustache_Engine $mustache)
    {
        $this->mustache = $mustache;
    }

    /**
     * Add multiple classes
     *
     * @param  array $classes
     * @return void
     */
    public function addClasses(array $classes)
    {
        foreach ($classes as $classname) {
            $this->addClass($classname);
        }
    }

    /**
     * Add class for processing
     *
     * @param  string $classname
     * @return void
     */
    public function addClass($classname)
    {
        if (!isset($this->controllerClasses[$classname])) {
            $reflClass = new ReflectionClass($classname);
            if ($reflClass->isController()) {
                $this->controllerClasses[$classname] = $reflClass;
            }
            if ($reflClass->isContainer()) {
                $this->setContainerClassName('\\'.$reflClass->getName());
            }
            if ($reflClass->isCaller()) {
                $this->setCallerClassName('\\'.$reflClass->getName());
            }
        }
    }

    /**
     * Set root path
     *
     * @param  string $root
     * @return void
     */
    public function setRoot($root)
    {
        assert('is_string($root)');
        $this->root = $root;
    }

    /**
     * Set caller class name
     *
     * @param  string $classname
     * @return void
     */
    public function setCallerClassName($classname)
    {
        assert('is_string($classname)');
        $this->caller = $classname;
    }

    /**
     * Set DI-container class name
     *
     * @param  string $classname
     * @return void
     */
    public function setContainerClassName($classname)
    {
        assert('is_string($classname)');
        $this->container = $classname;
    }

    /**
     * Get name of caller class
     *
     * @return string
     */
    public function getCallerClassName()
    {
        return $this->caller;
    }

    /**
     * Get name of DI-container class
     *
     * @return string
     */
    public function getContainerClassName()
    {
        return $this->container;
    }

    /**
     * Get DIC code
     *
     * @return string
     */
    public function getDependencyContainerCode()
    {
        $factories = array();

        /** @var ReflectionClass $controller */
        foreach ($this->controllerClasses as $controller) {
            $factories[] = array(
                'name'      => $controller->getFactoryName(),
                'class'     => $controller->getName(),
                'signature' => $controller->getSignature(),
                'params'    => $controller->getInjections()
            );
        }

        return $this->mustache->loadTemplate('Dependencies')
            ->render(array('factories' => $factories));
    }

    /**
     * Get route map code
     *
     * @return string
     */
    public function getRouteCode()
    {
        $routes = array();

        /** @var ReflectionClass $controller */
        foreach ($this->controllerClasses as $controller) {
            /** @var array $route */
            foreach ($controller->getRoutes() as $route) {
                $routes[] = array(
                    'name'         => $route['routename'],
                    'path'         => $route['path'],
                    'method'       => $route['httpmethod'],
                    'cntrlfactory' => $controller->getFactoryName(),
                    'cntrlmethod'  => $route['methodname']
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
     * @return string
     */
    public function generate()
    {
        return $this->getDependencyContainerCode()
            . $this->getRouteCode()
            . $this->getStaticCode();
    }
}
