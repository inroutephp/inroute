<?php

declare(strict_types = 1);

namespace spec\inroutephp\inroute\Runtime\Middleware;

use inroutephp\inroute\Runtime\Middleware\Pipeline;
use inroutephp\inroute\Runtime\Exception\DispatchException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PipelineSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Pipeline::CLASS);
    }

    function it_is_a_request_handler()
    {
        $this->shouldHaveType(RequestHandlerInterface::CLASS);
    }

    function it_throws_on_emptied_pipeline(ServerRequestInterface $request)
    {
        $this->shouldThrow(DispatchException::CLASS)->during('handle', [$request]);
    }

    function it_can_dispatch_single_middleware(
        MiddlewareInterface $middleware,
        ServerRequestInterface $request,
        ResponseInterface $response
    ) {
        $middleware->process($request, Argument::any())->willReturn($response);
        $this->beConstructedWith($middleware);
        $this->handle($request)->shouldReturn($response);
    }

    function it_can_dispatch_multiple_middlewares(
        MiddlewareInterface $middleware,
        ServerRequestInterface $request,
        ResponseInterface $response
    ) {
        $passingMiddleware = new class implements MiddlewareInterface {
            function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
            {
                return $handler->handle($request);
            }
        };

        $middleware->process($request, Argument::any())->willReturn($response)->shouldBeCalled();

        $this->beConstructedWith($passingMiddleware, $middleware);
        $this->handle($request)->shouldReturn($response);
    }
}
