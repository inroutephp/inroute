<?php

/**
 * The @inrouteContainer annotation tells inroute that this container should be used
 *
 * @inrouteContainer
 */
class Container extends \Pimple
{
    public function __construct()
    {
        // Create injection getDependency
        $this['getDependency'] = function ($c) {
            return "Dependency injected using DI-container";
        };
    }
}
