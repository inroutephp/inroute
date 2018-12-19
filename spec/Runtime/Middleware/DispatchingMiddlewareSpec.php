<?php

declare(strict_types = 1);

namespace spec\inroutephp\inroute\Runtime\Middleware;

use inroutephp\inroute\Runtime\Middleware\DispatchingMiddleware;
use inroutephp\inroute\Runtime\EnvironmentInterface;
use inroutephp\inroute\Runtime\Exception\DispatchException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DispatchingMiddlewareSpec extends ObjectBehavior
{
    function let(EnvironmentInterface $env)
    {
        $this->beConstructedWith(
            function () {
            },
            $env
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(DispatchingMiddleware::CLASS);
    }

    function it_is_a_middleware()
    {
        $this->shouldHaveType(MiddlewareInterface::CLASS);
    }

    function it_can_dispatch(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
        ResponseInterface $response,
        $env
    ) {
        $target = function ($passedRequest, $passedEnv) use ($request, $env, $response) {
            if ($passedRequest !== $request->getWrappedObject()) {
                throw new \Exception('Invalid request passed');
            }

            if ($passedEnv !== $env->getWrappedObject()) {
                throw new \Exception('Invalid environment passed');
            }

            return $response->getWrappedObject();
        };

        $this->beConstructedWith($target, $env);
        $this->process($request, $handler)->shouldReturn($response);
    }

    function it_throws_on_no_response(ServerRequestInterface $request, RequestHandlerInterface $handler, $env)
    {
        $this->shouldThrow(DispatchException::CLASS)->during('process', [$request, $handler]);
    }
}
