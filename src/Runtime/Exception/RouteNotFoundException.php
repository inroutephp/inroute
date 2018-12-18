<?php

declare(strict_types = 1);

namespace inroutephp\inroute\Runtime\Exception;

use Psr\Http\Message\ServerRequestInterface;

class RouteNotFoundException extends RequestException
{
    public function __construct(ServerRequestInterface $request, array $context = [])
    {
        parent::__construct("Route not found", 404, $request, $context);
    }
}
