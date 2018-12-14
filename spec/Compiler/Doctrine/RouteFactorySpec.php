<?php

declare(strict_types = 1);

namespace spec\inroutephp\inroute\Compiler\Doctrine;

use inroutephp\inroute\Compiler\Doctrine\RouteFactory;
use inroutephp\inroute\Compiler\RouteCollection;
use inroutephp\inroute\Runtime\Route;
use Doctrine\Common\Annotations\AnnotationReader;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RouteFactorySpec extends ObjectBehavior
{
    function let(AnnotationReader $reader)
    {
        $this->beConstructedWith($reader);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RouteFactory::CLASS);
    }

    function it_ignores_classes_that_does_not_exist()
    {
        $this->createRoutesFrom('this-class-does-not-exist')->shouldBeLike(new RouteCollection([]));
    }

    function it_ignores_non_instatiable_classes()
    {
        $this->createRoutesFrom(\FilterIterator::CLASS)->shouldBeLike(new RouteCollection([]));
    }

    function it_can_create_routes($reader)
    {
        $reader->getMethodAnnotations(Argument::any())->willReturn([]);

        $this->createRoutesFrom(RouteFactory::CLASS)->shouldBeLike(
            new RouteCollection([
                new Route(
                    RouteFactory::CLASS,
                    'createRoutesFrom',
                    []
                )
            ])
        );
    }
}
