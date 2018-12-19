<?php

declare(strict_types = 1);

namespace inroutephp\inroute\Runtime\Middleware;

use inroutephp\inroute\Runtime\EnvironmentInterface;
use inroutephp\inroute\Runtime\Exception\DispatchException;
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
        $response = ($this->target)($request, $this->environment);

        if (!$response instanceof ResponseInterface) {
            throw new DispatchException(
                'Dispatcher callable must return a ResponseInterface object. Found: ' . gettype($response)
            );
        }

        return $response;
    }
}
