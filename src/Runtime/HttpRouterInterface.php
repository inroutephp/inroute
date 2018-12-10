<?php

namespace inroutephp\inroute\Runtime;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Container\ContainerInterface;

interface HttpRouterInterface extends MiddlewareInterface
{
    /**
     * Set container used when dispatching routes
     */
    public function setContainer(ContainerInterface $container): void;
}
