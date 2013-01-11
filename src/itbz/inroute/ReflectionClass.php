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
     * Get a list of injections needed to instantiate relfected class
     *
     * @return array
     */
    public function getInjections()
    {
        if (!$this->hasConstructor()) {

            return array();
        }

        $injections = array();  // Array of injections to create for this class
        $required = array();    // List of required parameters

        // Loop over constructor parameters
        foreach ($this->getConstructor()->getParameters() as $param) {
            $key = '$' . $param->getName();
            $class = $param->getClass();

            if ($class) {
                $injections[$key] = array('class' => $class->getName());
            } else {
                $injections[$key] = array('class' => '');
            }

            if (!$param->isOptional()) {
                $required[$key] = true;
            }
        }

        // Loop over docblock inject tags
        $docblock = new DocBlock($this->getConstructor());
        foreach ($docblock->getTagsByName('inject') as $tag) {
            $values = array_filter(explode(" ", $tag->getDescription()));
            $key = reset($values);

            if (!isset($injections[$key])) {
                $msg = "Trying to inject unknown paramater $key into {$this->getName()}";
                throw new InjectionException($msg);
            }

            $injections[$key]['factory'] = end($values);
            unset($required[$key]);
        }

        // Check if any required parameter is missing
        if (!empty($required)) {
            $param = key($required);
            $msg = "Parameter $param must be injected into {$this->getName()}";
            throw new InjectionException($msg);
        }

        return $injections;
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
                    'class' => $this->getName(),
                    'method' => $method->getName(),
                    'desc' => $tags[0]->getDescription()
                );
            }
        }

        return $routes;
    }
}
