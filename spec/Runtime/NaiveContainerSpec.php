<?php

declare(strict_types = 1);

namespace spec\inroutephp\inroute\Runtime;

use inroutephp\inroute\Runtime\NaiveContainer;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class NaiveContainerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(NaiveContainer::CLASS);
    }

    function it_is_a_container()
    {
        $this->shouldHaveType(ContainerInterface::CLASS);
    }

    function it_does_not_have_unexisting_classes()
    {
        $this->has('this-is-not-a-class')->shouldReturn(false);
    }

    function it_does_not_have_non_instantiable_symbols()
    {
        $this->has(\FilterIterator::CLASS)->shouldReturn(false);
    }

    function it_does_not_have_classes_that_require_constructor_args()
    {
        $this->has(\IteratorIterator::CLASS)->shouldReturn(false);
    }

    function it_has_classes_instatiable_with_no_args()
    {
        $this->has(\RuntimeException::CLASS)->shouldReturn(true);
    }

    function it_throws_on_non_existing_service()
    {
        $this->shouldThrow(NotFoundExceptionInterface::CLASS)->during('get', ['this-is-not-a-class']);
    }

    function it_can_get_services()
    {
        $this->get(NaiveContainer::CLASS)->shouldBeLike(new NaiveContainer);
    }

    function it_cashes_services()
    {
        $this->get(NaiveContainer::CLASS)->shouldReturn(
            $this->get(NaiveContainer::CLASS)
        );
    }
}
