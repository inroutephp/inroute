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

use phpDocumentor\Reflection\DocBlock;
use iio\inroute\Exception\InjectionException;
use iio\inroute\Tag\ControllerTag;
use iio\inroute\Tag\RouteTag;

/**
 * ReflectionClass extension with docbloc parsing capabilities
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class ReflectionClass extends \ReflectionClass
{
    /**
     * @var array All constructor parameters
     */
    private $params;

    /**
     * @var ControllerTag Class controller tag
     */
    private $controllerTag;

    /**
     * Get class controller tag
     *
     * @return ControllerTag void if class is not a controller
     */
    public function getControllerTag()
    {
        if (!isset($this->controllerTag)) {
            /** @var ControllerTag $tag */
            foreach (ControllerTag::parseDocBlock(new DocBlock($this)) as $tag) {
                $this->controllerTag = $tag;
            }
        }

        return $this->controllerTag;
    }

    /**
     * Check if the reflected class is a controller
     *
     * Classes to be processed must be tagged with the @controller tag
     *
     * @return boolean
     */
    public function isController()
    {
        return !!$this->getControllerTag();
    }

    /**
     * Check if the reflected class is an DI-container
     *
     * @return boolean
     */
    public function isContainer()
    {
        return $this->implementsInterface('iio\inroute\ContainerInterface');
    }

    /**
     * Check if the reflected class is a caller
     *
     * @return boolean
     */
    public function isCaller()
    {
        return $this->implementsInterface('iio\inroute\CallerInterface');
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
                /** @var \ReflectionParameter $param */
                foreach ($this->getConstructor()->getParameters() as $param) {
                    $name = '$' . $param->getName();
                    $class = $param->getClass();
                    $classname = $class ? $class->getName() : '';
                    $this->params[$name] = array(
                        'name'  => $name,
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
     * @throws InjectionException If parameter found in tag does not exist
     * @throws InjectionException If any constructor parameter is not injected
     */
    public function getInjections()
    {
        $params = $this->getConstructorParams();
        $docblock = new DocBlock($this->getConstructor());

        /** @var ParamTag $tag */
        foreach ($docblock->getTagsByName('param') as $tag) {
            $name = $tag->getVariableName();

            if (!isset($params[$name])) {
                $msg = "Trying to inject unknown paramater $name into {$this->getName()}";
                throw new InjectionException($msg);
            }

            if (!preg_match('/inject:([^\s]+)/i', $tag->getDescription(), $matches)) {
                $msg = "Injection clause missing for parameter $name (use inject:xxx)";
                throw new InjectionException($msg);
            }

            $params[$name]['factory'] = $matches[1];
        }

        /** @var array $param */
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
     * The @route tag is definied as @route [method] <[path]>. [method] can be
     * a comma separeted list of http methods. Note that the list must NOT
     * contain any spaces. [path] is a path template and may contain regular
     * expressions matching subpaths.
     * 
     * @return array
     */
    public function getRoutes()
    {
        $routes = array();
        
        /** @var ReflectionMethod $method */
        foreach ($this->getMethods() as $method) {
            if ($method->isConstructor()) {
                continue;
            }

            /** @var RouteTag $tag  */
            foreach (RouteTag::parseDocBlock(new DocBlock($method)) as $tag) {
                $routes[] = array(
                    'methodname' => $method->getName(),
                    'routename'  => $this->getShortName() . '::' . $method->getName(),
                    'httpmethod' => $tag->getMethods(),
                    'path'       => $this->getControllerTag()->getPath() . $tag->getPath()
                );
            }
        }

        return $routes;
    }
}
