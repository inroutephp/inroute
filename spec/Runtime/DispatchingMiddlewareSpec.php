<?php

declare(strict_types = 1);

namespace spec\inroutephp\inroute\Runtime;

use inroutephp\inroute\Runtime\DispatchingMiddleware;
use inroutephp\inroute\Runtime\EnvironmentInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DispatchingMiddlewareSpec extends ObjectBehavior
{
    function it_is_initializable(EnvironmentInterface $env)
    {
        $this->beConstructedWith('printf', $env);
        $this->shouldHaveType(DispatchingMiddleware::CLASS);
    }

    function it_is_a_middleware(EnvironmentInterface $env)
    {
        $this->beConstructedWith('printf', $env);
        $this->shouldHaveType(MiddlewareInterface::CLASS);
    }

    function it_can_dispatch(
        EnvironmentInterface $env,
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
        ResponseInterface $response
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
}
