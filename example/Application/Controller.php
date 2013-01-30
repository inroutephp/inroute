<?php

use itbz\inroute\Route;

/**
 * The @inroute annotation tells inroute that this class should be scanned
 *
 * @inroute
 */
class Controller
{
    /**
     * The @inject annotation tells inroute that the $dependency parameter
     * should be created using DI-container method getDependency
     *
     * @inject $dependency getDependency
     */
    public function __construct($dependency)
    {
        $this->dependency = $dependency;
    }

    /**
     * The $route annotation tells inroute when to route requests to this method
     *
     * @route GET /application/{:name}
     */
    public function cntrl(Route $route, $customRequest)
    {
        $view = "Injected: <b>{$this->dependency}</b><br/>";
        $view .= "Routed path name parameter: <b>{$route->getValue('name')}</b><br/>";
        $view .= "Cutsom request var created in caller: <b>$customRequest</b><br/>";

        return $view;
    }

    /**
     * Hello world controller. Used when testing.
     *
     * @route POST /hello-world
     */
    public function postHelloWorld()
    {
        return 'POST hello world';
    }

    /**
     * Hello world controller. Used when testing.
     *
     * @route GET /hello-world
     */
    public function helloWorld()
    {
        return 'Hello world!';
    }

    /**
     * Controller using multiple methods. Used when testing.
     *
     * @route GET,POST /postAndGet
     */
    public function postAndGet()
    {
        return 'postAndGet';
    }
}
