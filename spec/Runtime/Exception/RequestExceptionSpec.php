<?php

declare(strict_types = 1);

namespace spec\inroutephp\inroute\Runtime\Exception;

use inroutephp\inroute\Runtime\Exception\RequestException;
use Psr\Http\Message\ServerRequestInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RequestExceptionSpec extends ObjectBehavior
{
    function let(ServerRequestInterface $request)
    {
        $this->beConstructedWith('message', 404, $request, ['context']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RequestException::CLASS);
    }

    function it_contains_a_request($request)
    {
        $this->getRequest()->shouldReturn($request);
    }

    function it_contains_a_context()
    {
        $this->getContext()->shouldReturn(['context']);
    }

    function it_contains_a_message()
    {
        $this->getMessage()->shouldReturn('message');
    }

    function it_contains_a_code()
    {
        $this->getCode()->shouldReturn(404);
    }
}
