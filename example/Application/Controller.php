<?php

use iio\inroute\Route;

/**
 * The @controller annotation tells inroute that this class should be scanned
 *
 * @controller </base>
 */
class Controller
{
    /**
     * The inject:xxx notation in the @param description tells inroute that the
     * $dependency parameter should be created using DI-container method getDependency
     *
     * @param mixed $dependency inject:getDependency
     */
    public function __construct($dependency)
    {
        $this->dependency = $dependency;
    }

    /**
     * The $route annotation tells inroute when to route requests to this method
     *
     * Use multiple $route tags th create aliases
     *
     * @route GET </app/{:name}>
     * @route GET </application/{:name}>
     */
    public function cntrl(Route $route, $customRequest)
    {
        $view = "Injected: <b>{$this->dependency}</b><br/>";
        $view .= "Url used: <b>{$route->generate('Controller::cntrl')}</b><br/>";
        $view .= "Routed path name parameter: <b>{$route->getValue('name')}</b><br/>";
        $view .= "Cutsom request var created in caller: <b>$customRequest</b><br/>";

        return $view;
    }

    /**
     * Hello world controller. Used when testing.
     *
     * @route POST </hello-world>
     */
    public function postHelloWorld()
    {
        return 'POST hello world';
    }

    /**
     * Hello world controller. Used when testing.
     *
     * @route GET </hello-world>
     */
    public function helloWorld()
    {
        return 'Hello world!';
    }

    /**
     * Controller using multiple methods. Used when testing.
     *
     * @route GET,POST </postAndGet>
     */
    public function postAndGet()
    {
        return 'postAndGet';
    }
}
