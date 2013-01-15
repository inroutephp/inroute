<?php
/**
 * This file is part of the inroute package
 *
 * Copyright (c) 2013 Hannes Forsgård
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package itbz\inroute
 */

namespace itbz\inroute;

use phpDocumentor\Reflection\DocBlock;
use itbz\inroute\Exception\InjectionException;

/**
 * ReflectionClass extension with docbloc parsing capabilities
 *
 * @package itbz\inroute
 */
class ReflectionClass extends \ReflectionClass
{
    /**
     * All constructor parameters
     *
     * @var array
     */
    private $params;

    /**
     * Check if the reflected class should be processed
     *
     * Classes to be processed must be tagged with the @inroute tag
     *
     * @return boolean
     */
    public function isInroute()
    {
        $docblock = new DocBlock($this);
        return $docblock->hasTag('inroute');
    }

    /**
     * Check if reflected class has a constructor
     *
     * @return boolean
     */
    public function hasConstructor()
    {
        return !!$this->getConstructor();
    }

    /**
     * Get name usable for naming methods
     *
     * @return string
     */
    public function getFactoryName()
    {
        return str_replace('\\', '_', $this->getName());
    }

    /**
     * Get array of constructor parameters of reflected class
     *
     * Array keys are the parameter names. Array values are arrays containing
     * name, optional classname and flag if parameter is expected to be an
     * array.
     * 
     * @return array
     */
    public function getConstructorParams()
    {
        if (!isset($this->params)) {
            $this->params = array();
            if ($this->hasConstructor()) {
                foreach ($this->getConstructor()->getParameters() as $param) {
                    $name = '$' . $param->getName();
                    $class = $param->getClass();
                    $classname = $class ? $class->getName() : '';
                    $this->params[$name] = array(
                        'name' => $name,
                        'class' => $classname,
                        'array' => $param->isArray()
                    );
                }
            }
        }

        return $this->params;
    }

    /**
     * Get constructor signature of reflected class
     *
     * Does not return a true signature. Class name is not included, nor are
     * parameter types. Returns a string with is a list of comma separated
     * parameter names.
     * 
     * @return string
     */
    public function getSignature()
    {
        return implode(', ', array_keys($this->getConstructorParams()));
    }

    /**
     * Get a list of injections needed to instantiate relfected class
     *
     * Note that all constructor arguments must be injected, this includes
     * parameters with default values (that on the language level are optional).
     *
     * @return array Returns an array of arrays, where the inner arrays contain
     * name, class, array flag and factory values.
     *
     * @throws InjectionException If param found in @inject tag does not exist
     * @throws InjectionException If any constructor parameter is not injected
     */
    public function getInjections()
    {
        $params = $this->getConstructorParams();

        $docblock = new DocBlock($this->getConstructor());
        foreach ($docblock->getTagsByName('inject') as $tag) {
            list($name, $factory) = array_filter(explode(" ", $tag->getDescription()));
            if (!isset($params[$name])) {
                $msg = "Trying to inject unknown paramater $name into {$this->getName()}";
                throw new InjectionException($msg);
            }
            $params[$name]['factory'] = $factory;
        }

        // Is required parameter missing?
        foreach ($params as $param) {
            if (!isset($param['factory'])) {
                $msg = "Parameter {$param['name']} must be injected into {$this->getName()}";
                throw new InjectionException($msg);
            }
        }

        return array_values($params);
    }

    /**
     * Get a list of routes defined in relfected class
     *
     * @return array
     */
    public function getRoutes()
    {
        $routes = array();
        foreach ($this->getMethods() as $method) {
            if ($method->isConstructor()) {
                continue;
            }
            $docblock = new DocBlock($method);
            if ($docblock->hasTag('route')) {
                $tags = $docblock->getTagsByName('route');
                $routes[] = array(
                    'name' => $method->getName(),
                    'desc' => $tags[0]->getDescription()
                );
            }
        }

        return $routes;
    }
}
