<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\Router;

/**
 * The route class
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class Route
{
    /**
     * @var array Path tokens
     */
    private $tokens;

    /**
     * @var Regex Regular expression used when generating paths
     */
    private $regex;

    /**
     * @var Environment Route environment
     */
    private $env;

    /**
     * @var string[] List of pre filter classnames
     */
    private $preFilters;

    /**
     * @var string[] List of post filter classnames
     */
    private $postFilters;

    /**
     * @var string Mathed HTTP method
     */
    private $methodMatch = '';

    /**
     * @var string Matched path
     */
    private $pathMatch = '';

    /**
     * Constructor
     *
     * @param array       $tokens      Path tokens used when generating paths
     * @param Regex       $regex       Regular expression used when matching a path
     * @param Environment $env         Route environment
     * @param string[]    $preFilters  List of pre filter classnames
     * @param string[]    $postFilters List of post filter classnames
     */
    public function __construct(
        array $tokens,
        Regex $regex,
        Environment $env,
        array $preFilters,
        array $postFilters
    ) {
        $this->tokens = $tokens;
        $this->regex = $regex;
        $this->env = $env;
        $this->preFilters = $preFilters;
        $this->postFilters = $postFilters;
    }

    /**
     * Execute route
     *
     * @param  callable $instantiator
     * @return mixed    Whatever the controller returns
     */
    public function execute(callable $instantiator)
    {
        $this->env->set('route', $this);

        foreach ($this->preFilters as $filtername) {
            $this->instantiateAndExecute($filtername, 'filter', $this->env, $instantiator);
        }

        $returnValue = $this->instantiateAndExecute(
            $this->env->get('controller_name'),
            $this->env->get('controller_method'),
            $this->env,
            $instantiator
        );

        foreach ($this->postFilters as $filtername) {
            $returnValue = $this->instantiateAndExecute($filtername, 'filter', $returnValue, $instantiator);
        }

        return $returnValue;
    }

    /**
     * Create instance of $classname and execute $methodname with $arg
     *
     * @param  string   $classname
     * @param  string   $methodname
     * @param  mixed    $arg
     * @param  callable $instantiator
     * @return mixed    Whatever method returns
     */
    private function instantiateAndExecute($classname, $methodname, $arg, callable $instantiator)
    {
        return call_user_func(
            [
                call_user_func($instantiator, $classname),
                $methodname
            ],
            $arg
        );
    }

    /**
     * Check if route matches HTTP method
     *
     * @param  string  $method
     * @return boolean
     */
    public function isMethodMatch($method)
    {
        if (in_array($method, (array)$this->env->get('http_methods'))) {
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
        return sprintf(
            '%s::%s',
            $this->env->get('controller_name'),
            $this->env->get('controller_method')
        );
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
     * @throws \RuntimeException If any parameter is missing
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
                throw new \RuntimeException("Parameter <{$token->getName()}> missing.");
            }

            $parts[] = $token->substitute($params[$token->getName()]);
        }

        return implode('/', $parts);
    }
}
