<?php

declare(strict_types = 1);

namespace spec\inroutephp\inroute\Runtime\Middleware;

use inroutephp\inroute\Runtime\Middleware\CallbackHandler;
use inroutephp\inroute\Runtime\Exception\DispatchException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CallbackHandlerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith('is_null');
        $this->shouldHaveType(CallbackHandler::CLASS);
    }

    function it_is_a_request_handler()
    {
        $this->beConstructedWith('is_null');
        $this->shouldHaveType(RequestHandlerInterface::CLASS);
    }

    function it_executes_callable(ServerRequestInterface $request, ResponseInterface $response)
    {
        $this->beConstructedWith(function ($passedRequest) use ($request, $response) {
            if ($passedRequest !== $request->getWrappedObject()) {
                throw new \Exception('Request should be passed');
            }

            return $response->getWrappedObject();
        });

        $this->handle($request)->shouldReturn($response);
    }

    function it_fails_if_no_response_is_returned(ServerRequestInterface $request)
    {
        $this->beConstructedWith('is_null');
        $this->shouldThrow(DispatchException::CLASS)->during('handle', [$request]);
    }
}
