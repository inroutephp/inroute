<?php

declare(strict_types = 1);

namespace spec\inroutephp\inroute\Runtime;

use inroutephp\inroute\Runtime\Route;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RouteSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('', '', []);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Route::CLASS);
    }

    function it_can_have_annotation()
    {
        $this->beConstructedWith('', '', [new \RuntimeException]);
        $this->hasAnnotation(\RuntimeException::CLASS)->shouldReturn(true);
        $this->hasAnnotation(\LogicException::CLASS)->shouldReturn(false);
    }

    function it_can_get_annotation()
    {
        $annot = new \RuntimeException;
        $this->beConstructedWith('', '', [$annot]);
        $this->getAnnotation(\RuntimeException::CLASS)->shouldReturn($annot);
    }

    function it_returns_null_on_unknown_annotation()
    {
        $this->beConstructedWith('', '', []);
        $this->getAnnotation(\RuntimeException::CLASS)->shouldReturn(null);
    }

    function it_can_get_all_annotations()
    {
        $annot = new \RuntimeException;
        $this->beConstructedWith('', '', [$annot, $annot]);
        $this->getAnnotations()->shouldReturn([$annot, $annot]);
    }

    function it_can_get_some_annotations()
    {
        $annot = new \RuntimeException;
        $this->beConstructedWith('', '', [$annot, new \LogicException]);
        $this->getAnnotations(\RuntimeException::CLASS)->shouldReturn([$annot]);
    }

    function it_has_a_default_name()
    {
        $this->beConstructedWith('service', 'method', []);
        $this->getName()->shouldReturn('service:method');
    }

    function it_can_set_name()
    {
        $this->withName('foobar')->shouldReturnRouteThat(function (Route $route) {
            return $route->getName() == 'foobar';
        });
    }

    function it_is_routable_by_default()
    {
        $this->shouldNotBeRoutable();
    }

    function it_can_set_routable()
    {
        $this->withRoutable(true)->shouldReturnRouteThat(function (Route $route) {
            return $route->isRoutable() == true;
        });
    }

    function it_can_set_http_method()
    {
        $this->withHttpMethod('POST')->shouldReturnRouteThat(function (Route $route) {
            return $route->getHttpMethods() == ['POST'];
        });
    }

    function it_can_unset_http_method()
    {
        $this->withHttpMethod('GET')->withoutHttpMethod('GET')->shouldReturnRouteThat(function (Route $route) {
            return $route->getHttpMethods() == [];
        });
    }

    function it_understands_case_insensitive_http_methods()
    {
        $this->withHttpMethod('post')->shouldReturnRouteThat(function (Route $route) {
            return $route->getHttpMethods() == ['POST'];
        });
    }

    function it_can_set_path()
    {
        $this->withPath('foobar')->shouldReturnRouteThat(function (Route $route) {
            return $route->getPath() == 'foobar';
        });
    }

    function it_can_check_attibute()
    {
        $this->hasAttribute('foo')->shouldReturn(false);
    }

    function it_can_set_attributes()
    {
        $this->withAttribute('foo', 'bar')->shouldReturnRouteThat(function (Route $route) {
            return $this->hasAttribute('foo')
                && $route->getAttribute('foo') == 'bar'
                && $route->getAttributes() == ['foo' => 'bar'];
        });
    }

    function it_defaults_to_service_id()
    {
        $this->beConstructedWith('foo', '', []);
        $this->getServiceId()->shouldReturn('foo');
    }

    function it_can_set_service_id()
    {
        $this->withServiceId('foobar')->shouldReturnRouteThat(function (Route $route) {
            return $route->getServiceId() == 'foobar';
        });
    }

    function it_defaults_to_service_method()
    {
        $this->beConstructedWith('', 'foo', []);
        $this->getServiceMethod()->shouldReturn('foo');
    }

    function it_can_set_service_method()
    {
        $this->withServiceMethod('foobar')->shouldReturnRouteThat(function (Route $route) {
            return $route->getServiceMethod() == 'foobar';
        });
    }

    function it_can_set_middleware_service_ids()
    {
        $this->withMiddleware('foo')->withMiddleware('bar')->shouldReturnRouteThat(function (Route $route) {
            return $route->getMiddlewareServiceIds() == ['foo', 'bar'];
        });
    }

    function getMatchers(): array
    {
        return [
            'returnRouteThat' => function (Route $route, callable $predicate) {
                return $predicate($route);
            },
        ];
    }
}
