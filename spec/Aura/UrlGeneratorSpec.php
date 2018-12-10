<?php

declare(strict_types = 1);

namespace spec\inroutephp\inroute\Aura;

use inroutephp\inroute\Aura\UrlGenerator;
use Aura\Router\Generator;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class UrlGeneratorSpec extends ObjectBehavior
{
    function let(Generator $generator)
    {
        $this->beConstructedWith($generator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(UrlGenerator::CLASS);
    }

    function it_can_generate_urls($generator)
    {
        $generator->generate('name', ['foo' => 'bar'])->willReturn('foobar')->shouldBeCalled();
        $this->generateUrl('name', ['foo' => 'bar'])->shouldReturn('foobar');
    }
}
