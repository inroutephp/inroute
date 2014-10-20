<?php
namespace inroute\example;

// If you are using inroute as a require-dev dependency
// you should only use classes from the runtime sub-package
use inroute\Runtime\Routable;
use inroute\Runtime\Environment;

/**
 * @controllerBasePath </path>
 */
class Controller implements Routable
{
    /**
     * The route annotation tells inroute when to route requests to this method
     *
     * @route GET </foo/{:name}>
     */
    public function read(Environment $env)
    {
        // Read path paramterer
        return $route->generate('Working::bar');
    }

    /**
     * Using multiple HTTP methods
     *
     * @route GET,POST </postAndGet>
     */
    public function bar(Environment $env)
    {
    }
}
