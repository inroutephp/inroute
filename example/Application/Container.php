<?php

/**
 * Implementing the ContainerInterface tells inroute this container should be used
 */
class Container extends \Pimple implements iio\inroute\ContainerInterface
{
    public function __construct()
    {
        // Create injection getDependency
        $this['getDependency'] = function ($c) {
            return "Dependency injected using DI-container";
        };
    }
}
