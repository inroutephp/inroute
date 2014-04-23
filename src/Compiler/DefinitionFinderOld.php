<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\Compiler;

use ReflectionClass;
use IteratorAggregate;
use ArrayIterator;
use phpDocumentor\Reflection\DocBlock;
use inroute\Tag\ControllerTag;
use inroute\Tag\RouteTag;
use inroute\Exception\RuntimeException;

/**
 * Extract route descriptions from controller class
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class DefinitionFinderOld extends ReflectionClass implements IteratorAggregate
{
    /**
     * @var ControllerTag Class controller tag
     */
    private $controllerTag;

    /**
     * Classes to be processed must be tagged with the @controller tag
     *
     * @param mixed $class Either a string containing the name of the class to reflect, or an object.
     */
    public function __construct($class)
    {
        parent::__construct($class);

        /** @var ControllerTag $tag */
        foreach (ControllerTag::parseDocBlock(new DocBlock($this)) as $tag) {
            $this->controllerTag = $tag;
        }

        if (!isset($this->controllerTag)) {
            throw new RuntimeException("<{$this->getName()}> is not an inroute controller.");
        }
    }

    /**
     * Get a list of routes defined in relfected class
     *
     * The @route tag is definied as @route [method] <[path]>. [method] can be
     * a comma separeted list of http methods. Note that the list must NOT
     * contain any spaces. [path] is a path template and may contain regular
     * expressions matching subpaths.
     * 
     * @return ArrayIterator
     */
    public function getIterator()
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
                    'controller'       => $this->getName(),
                    'controllerMethod' => $method->getName(),
                    'httpmethods'      => $tag->getMethods(),
                    'path'             => $this->controllerTag->getPath() . $tag->getPath()
                );
            }
        }

        return new ArrayIterator($routes);
    }
}
