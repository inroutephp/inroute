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
    private $constructorParams;

    /**
     * Parameters required for injection to work
     *
     * @var array
     */
    private $requiredInjections;

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

        $params = $this->getConstructorParams();
        $required = $this->getRequiredInjections();
        $injections = array();
        $docblock = new DocBlock($this->getConstructor());

        foreach ($docblock->getTagsByName('inject') as $tag) {
            list($key, $factory) = array_filter(explode(" ", $tag->getDescription()));

            if (!isset($params[$key])) {
                $msg = "Trying to inject unknown paramater $key into {$this->getName()}";
                throw new InjectionException($msg);
            }

            $injections[$params[$key][0]] = array(      // Keep the original order
                'name' => $key,
                'class' => $params[$key][1],
                'factory' => $factory
            );

            unset($required[$key]);                     // Requirement satisfied
        }
        ksort($injections);

        if (!empty($required)) {                        // Is required parameter missing
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
                    'name' => $method->getName(),
                    'desc' => $tags[0]->getDescription()
                );
            }
        }

        return $routes;
    }

    /**
     * Get parameters required for injection to work
     *
     * @return array
     */
    public function getRequiredInjections()
    {
        if (!is_array($this->requiredInjections)) {
            $this->parseConstructorParams();
        }

        return $this->requiredInjections;
    }

    /**
     * Get array describing constructor parameters
     *
     * @return array
     */
    public function getConstructorParams()
    {
        if (!is_array($this->constructorParams)) {
            $this->parseConstructorParams();
        }

        return $this->constructorParams;
    }

    /**
     * Parse constructor parameters and fill private fields
     *
     * @return void
     */
    private function parseConstructorParams()
    {
        $this->requiredInjections = array();
        $this->constructorParams = array();

        if ($this->hasConstructor()) {
            foreach ($this->getConstructor()->getParameters() as $count => $param) {
                $key = '$' . $param->getName();
                $class = $param->getClass();

                if ($class) {
                    $this->constructorParams[$key] = array($count, $class->getName());
                } else {
                    $this->constructorParams[$key] = array($count, '');
                }

                if (!$param->isOptional()) {
                    $this->requiredInjections[$key] = true;
                }
            }
        }
    }
}
