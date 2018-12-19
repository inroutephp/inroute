<?php

declare(strict_types = 1);

namespace inroutephp\inroute\Runtime\Middleware;

use inroutephp\inroute\Runtime\Exception\DispatchException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class Pipeline implements RequestHandlerInterface
{
    /**
     * @var RequestHandlerInterface
     */
    private $handler;

    public function __construct(MiddlewareInterface ...$middlewares)
    {
        $this->handler = new CallbackHandler(function () {
            throw new DispatchException('unresolved request: middleware stack exhausted with no result');
        });

        foreach (array_reverse($middlewares) as $middleware) {
            $inner = $this->handler;
            $this->handler = new CallbackHandler(function (ServerRequestInterface $request) use ($middleware, $inner) {
                return $middleware->process($request, $inner);
            });
        }
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->handler->handle($request);
    }
}
