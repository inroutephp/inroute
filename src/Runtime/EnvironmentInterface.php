<?php

namespace inroutephp\inroute\Runtime;

/**
 * The environment of the current path
 */
interface EnvironmentInterface
{
    /**
     * Get route definition
     */
    public function getRoute(): RouteInterface;

    /**
     * Get url generator
     */
    public function getUrlGenerator(): UrlGeneratorInterface;
}
