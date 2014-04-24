<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\Router;

use inroute\Exception\RuntimeException;
use Closure;

/**
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class Route
{
    private $tokens, $regex, $httpMethods, $controller, $controllerMethod, $preFilters, $postFilters, $methodMatch = '', $pathMatch = '';

    /**
     * @param array     $tokens           Path tokens used when generating paths
     * @param Regex     $regex            Regular expression used when matching a path
     * @param array     $httpMethods      Array of routable http methods
     * @param string    $controller       Controller class name
     * @param string    $controllerMethod Controller method name
     * @param Closure[] $preFilters       Filters executed pre route
     * @param Closure[] $postFilters      Filters executed post route
     */
    public function __construct(array $tokens, Regex $regex, array $httpMethods, $controller, $controllerMethod, array $preFilters, array $postFilters)
    {
        $this->tokens = $tokens;
        $this->regex = $regex;
        $this->httpMethods = $httpMethods;
        $this->controller = $controller;
        $this->controllerMethod = $controllerMethod;
        $this->preFilters = $preFilters;
        $this->postFilters = $postFilters;
    }

    /**
     * Execute route
     *
     * @param  Closure $caller
     * @return mixed Whatever the defined route returns
     * @todo   Use Closure->bindTo() instead of sending $this in argument list
     */
    public function invoke(Closure $caller)
    {
        $args = array(
            'controller' => $this->controller,
            'method' => $this->controllerMethod,
            'route' => $this
        );

        /** @var Closure $filter */
        foreach ($this->preFilters as $filter) {
            $filter($args);
        }

        $return = $caller($args);

        /** @var Closure $filter */
        foreach ($this->postFilters as $filter) {
            $filter($return);
        }

        return $return;
    }

    /**
     * Check if route matches HTTP method
     *
     * @param  string  $method
     * @return boolean
     */
    public function isMethodMatch($method)
    {
        if (in_array($method, $this->httpMethods)) {
            $this->methodMatch = $method;
            return true;
        }
        $this->methodMatch = '';
        return false;
    }

    /**
     * Check if route matches path
     *
     * @param  string  $path
     * @return boolean
     */
    public function isPathMatch($path)
    {
        if ($this->regex->match($path)) {
            $this->pathMatch = $path;
            return true;
        }
        $this->pathMatch = '';
        return false;
    }

    /**
     * Get route name
     *
     * @return string
     */
    public function getName()
    {
        return "{$this->controller}::{$this->controllerMethod}";
    }

    /**
     * Get mathcing HTTP method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->methodMatch;
    }

    /**
     * Get matching request path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->pathMatch;
    }

    /**
     * Get matched path parameter
     *
     * @param  string $key parameter name
     * @return string
     */
    public function __get($key)
    {
        return $this->regex->$key;
    }

    /**
     * Generate path using parameters
     *
     * @param  array  $params List of named parameters
     * @return string
     */
    public function generate(array $params)
    {
        $parts = array();

        foreach ($this->tokens as $token) {
            if (is_string($token)) {
                $parts[] = $token;
                continue;
            }

            if (!isset($params[$token->getName()])) {
                throw new RuntimeException("Parameter <{$token->getName()}> missing.");
            }

            $parts[] = $token->substitute($params[$token->getName()]);
        }

        return implode('/', $parts);
    }
}
