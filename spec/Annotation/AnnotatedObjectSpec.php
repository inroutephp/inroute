<?php

declare(strict_types = 1);

namespace spec\inroutephp\inroute\Annotation;

use inroutephp\inroute\Annotation\AnnotatedObject;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AnnotatedObjectSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(AnnotatedObject::CLASS);
    }

    function it_has_annotations()
    {
        $this->beConstructedWith([new \RuntimeException]);
        $this->hasAnnotation(\RuntimeException::CLASS)->shouldReturn(true);
        $this->hasAnnotation(\LogicException::CLASS)->shouldReturn(false);
    }

    function it_can_get_annotations()
    {
        $annot = new \RuntimeException;
        $this->beConstructedWith([$annot]);
        $this->getAnnotation(\RuntimeException::CLASS)->shouldReturn($annot);
    }

    function it_returns_null_on_unknown_annotation()
    {
        $this->beConstructedWith([]);
        $this->getAnnotation(\RuntimeException::CLASS)->shouldReturn(null);
    }

    function it_can_get_all_annotations()
    {
        $annot = new \RuntimeException;
        $this->beConstructedWith([$annot, $annot]);
        $this->getAnnotations()->shouldReturn([$annot, $annot]);
    }

    function it_can_get_some_annotations()
    {
        $annot = new \RuntimeException;
        $this->beConstructedWith([$annot, new \LogicException]);
        $this->getAnnotations(\RuntimeException::CLASS)->shouldReturn([$annot]);
    }
}
