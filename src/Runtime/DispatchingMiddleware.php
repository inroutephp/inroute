<?php

declare(strict_types = 1);

namespace inroutephp\inroute\Runtime;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class DispatchingMiddleware implements MiddlewareInterface
{
    /**
     * @var callable
     */
    private $target;

    /**
     * @var EnvironmentInterface
     */
    private $environment;

    public function __construct(callable $target, EnvironmentInterface $environment)
    {
        $this->target = $target;
        $this->environment = $environment;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return ($this->target)($request, $this->environment);
    }
}
