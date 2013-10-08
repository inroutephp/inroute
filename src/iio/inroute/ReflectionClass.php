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
     * Get a list of defined injections
     *
     * @return array              Array of arrays
     * @throws InjectionException If inject clause is missing from @param tag
     * @throws InjectionException If a constructor parameter is not injected
     */
    public function getInjections()
    {
        if (!$this->hasConstructor()) {
            return array();
        }

        $injections = array();
        $docblock = new DocBlock($this->getConstructor());

        /** @var ParamTag $tag */
        foreach ($docblock->getTagsByName('param') as $tag) {
            if (!preg_match('/inject:([^\s]+)/i', $tag->getDescription(), $matches)) {
                $msg = "Injection missing for param {$tag->getVariableName()} (use inject:xxx)";
                throw new InjectionException($msg);
            }

            $injections[$tag->getVariableName()] = array('factory' => $matches[1]);
        }

        /** @var ReflectionParameter $param */
        foreach ($this->getConstructor()->getParameters() as $param) {
            $name = '$' . $param->getName();
            if (isset($injections[$name])) {
                $injections[$name]['params'] = array(
                    'name'    => $name,
                    'class'   => $param->getClass() ? $param->getClass()->getName() : '',
                    'isArray' => $param->isArray(),
                );
            } elseif (!$param->isOptional()) {
                $msg = "Parameter {$name} must be injected into {$this->getName()}";
                throw new InjectionException($msg);
            }
        }

        return array_values($injections);
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
