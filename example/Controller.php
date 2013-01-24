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
     * @route GET /foo/{:name}
     */
    public function cntrl(Route $route, $customRequest)
    {
        $view = "Injected: <b>{$this->dependency}</b><br/>";
        $view .= "Routed path name parameter: <b>{$route->getValue('name')}</b><br/>";
        $view .= "Cutsom request var created in caller: <b>$customRequest</b><br/>";

        return $view;
    }
}
