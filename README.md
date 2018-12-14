# ![Inroute](res/logo.png "Inroute")

[![Packagist Version](https://img.shields.io/packagist/v/inroutephp/inroute.svg?style=flat-square)](https://packagist.org/packages/inroutephp/inroute)
[![Build Status](https://img.shields.io/travis/inroutephp/inroute/master.svg?style=flat-square)](https://travis-ci.org/inroutephp/inroute)
[![Quality Score](https://img.shields.io/scrutinizer/g/inroutephp/inroute.svg?style=flat-square)](https://scrutinizer-ci.com/g/inroutephp/inroute)

Generate http routing and dispatching middleware from docblock annotations.

Inroute is a code generator. It scans your source tree for annotated routes and
generates a [PSR-15](https://www.php-fig.org/psr/psr-15/) compliant http routing
middleware. In addition all routes have a middleware pipeline of their own,
making it easy to add behaviour at compile time based on custom annotations.

* See the [example-app](https://github.com/inroutephp/example-app) for a
  complete example.
* See [console](https://github.com/inroutephp/console) for a compiler tool for
  the command line.

## Installation

```shell
composer require inroutephp/inroute:^1.0@beta
```

## Table of contents

1. [Writing routes](#writing-routes)
1. [Piping a route through a middleware](#piping-a-route-through-a-middleware)
1. [Compiling](#compiling)
1. [Dispatching](#dispatching)
1. [Generating route paths](#generating-route-paths)
1. [Creating custom annotations](#creating-custom-annotations)
1. [Processing routes using compiler passes](#processing-routes-using-compiler-passes)
1. [Handling dependencies with a DI container](#handling-dependencies-with-a-di-container)

## Writing routes

Routes are annotated using a doctrine annotations, are called with
a [PSR-7](https://www.php-fig.org/psr/psr-7/) request object and inroute
[environment](src/Runtime\EnvironmentInterface.php) and are expected to
return a PSR-7 response.

<!-- @example UserController -->
```php
use inroutephp\inroute\Annotations\BasePath;
use inroutephp\inroute\Annotations\GET;
use inroutephp\inroute\Runtime\EnvironmentInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\TextResponse;

/**
 * @BasePath(path="/users")
 */
class UserController
{
    /**
     * @GET(
     *     path="/{name}",
     *     name="getUser",
     *     attributes={
     *         "key": "value",
     *         "name": "overwritten by path value"
     *     }
     * )
     */
    function getUser(ServerRequestInterface $request, EnvironmentInterface $environment): ResponseInterface
    {
        return new TextResponse(
            // the name attribute from the request path
            $request->getAttribute('name')

            // the custom route attribute
            . $request->getAttribute('key')
        );
    }
}
```

* The `method` and `path` values are self explanatory.
* A route `name` is optional, and defaults to `class:method` (in the example
  `UserController:getUser`).
* `Attributes` are custom values that can be accessed at runtime through the
  request object.
* Note that the use of zend diactoros as a psr-7 response implementation is
  used in this example, you may of course use  another psr-7 implementation.

## Piping a route through a middleware

Each route has a [PSR-15](https://www.php-fig.org/psr/psr-15/) middleware
pipeline of its own. Adding a middleware to a route can be done using the
`@Pipe` annotation.

In the following example the `pipedAction` route is piped through the
`AppendingMiddleware` and the text `::Middleware` is appended to the route
response.

<!--
    @example PipedController
    @include UserController
-->
```php
use inroutephp\inroute\Annotations\Pipe;

class PipedController
{
    /**
     * @GET(path="/piped")
     * @Pipe(middlewares={"AppendingMiddleware"})
     */
    function pipedAction(ServerRequestInterface $request, EnvironmentInterface $environment): ResponseInterface
    {
        return new TextResponse('Controller');
    }
}

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AppendingMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        return new TextResponse(
            $response->getBody()->getContents() . "::Middleware"
        );
    }
}
```

## Compiling

The recommended way of building a project is by using the
[console](https://github.com/inroutephp/console) build tool. Compiling from
pure php involves setting up the compiler something like the following.

<!--
    @example Router
    @include PipedController
-->
```php
use inroutephp\inroute\Compiler\CompilerFacade;
use inroutephp\inroute\Compiler\Settings\ArraySettings;

$settings = new ArraySettings([
    'source-classes' => [
        UserController::CLASS,
        PipedController::CLASS,
    ],
    'target-namespace' => 'example',
    'target-classname' => 'HttpRouter',
]);

$facade = new CompilerFacade;

$code = $facade->compileProject($settings);

eval($code);

$router = new example\HttpRouter;
```

Possible settings include

* `container`: The classname of a compile time container, specify if needed.
* `bootstrap`: Classname of compile bootstrap, default should normally be fine.
* `source-dir`: Directory to scan for annotated routes.
* `source-prefix`: psr-4 namespace prefix to use when scanning directory.
* `source-classes`: Array of source classnames, use instead of or togheter with
   directory scanning.
* `route-factory`: Classname of route factory, default should normally be fine.
* `compiler`: Classname of compiler to use, default should normally be fine.
* `core-compiler-passes`: Array of core compiler passes, default should normally be fine.
* `compiler-passes`: Array of custom compiler passes.
* `code-generator`: The code generator to use, default should normally be fine.
* `target-namespace`: The namespace of the generated router (defaults to no namespace).
* `target-classname`: The classname of the generated router (defaults to `HttpRouter`).

### OpenApi

> Please note that reading openapi annotations is still very rudimentary. Please
> open an issue if you have suggestions on more values that should be parsed.

Instead of using the built in annotations inroute is also able to build openapi
projects annotated with [swagger-php](https://github.com/zircote/swagger-php)
annotations.

Set the `core-compiler-passes` setting to `['inroutephp\inroute\OpenApi\OpenApiCompilerPass']`.

## Dispatching

The generated router is a [PSR-15](https://www.php-fig.org/psr/psr-15/)
compliant middleware. To dispatch you need to supply an implementation of
[PSR-7](https://www.php-fig.org/psr/psr-7/) for request and response objects
and some response emitting functionality (of course you should also use a
complete middleware pipeline for maximum power).

In this simple example we use

* [zend-diactoros](https://github.com/zendframework/zend-diactoros) as PSR-15
  implementation and
* [zend-httphandlerrunner](https://github.com/zendframework/zend-httphandlerrunner)
  for emitting responses.
* [middleman](https://github.com/mindplay-dk/middleman) for dispatching the
  middleware pipeline.

<!--
    @example Dispatching
    @include Router
    @expectOutput foovalue
-->
```php
use Zend\Diactoros\ServerRequestFactory;
use mindplay\middleman\Dispatcher;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

// create a simple middleware pipeline for the entire appilcation
$dispatcher = new Dispatcher([$router]);

// create a psr-7 compliant response emitter
$emitter = new SapiEmitter;

// fakeing a GET request
$request = (new ServerRequestFactory)->createServerRequest('GET', '/users/foo');

// in the real worl you would of course use
// $request = ServerRequestFactory::fromGlobals();

// create the response
$response = $dispatcher->dispatch($request);

// send it
$emitter->emit($response);
```

Or to send to piped example from above:

<!--
    @example DispatchingPipedRoute
    @include Router
    @expectOutput Controller::Middleware
-->
```php
use Zend\Diactoros\ServerRequestFactory;
use mindplay\middleman\Dispatcher;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

(new SapiEmitter)->emit(
    (new Dispatcher([$router]))->dispatch(
        (new ServerRequestFactory)->createServerRequest('GET', '/piped')
    )
);
```

## Generating route paths

```php
function getUser(ServerRequestInterface $request, EnvironmentInterface $environment): ResponseInterface
{
    return new TextResponse(
        $environment->getUrlGenerator()->generateUrl('getUser', ['name' => 'myUserName'])
    );
}
```

## Creating custom annotations

Inroute uses [doctrine](https://github.com/doctrine/annotations) to read
annotations. Creating custom annotations is as easy as:

```php
namespace MyNamespace;

/** @Annotation */
class MyAnnotation
{
    public $value;
}
```

To create annotations that automatically pipes a route through a middleware use
something like the following.

> NOTE that you need to supply the `AuthMiddleware` to authenticate a user and
> the `RequireUserGroupMiddleware` to check user priviliges for this example to
> function as expected. See below on how to inject a dependency container to
> create that can deliver these middlewares.

```php
use inroutephp\inroute\Annotations\Pipe;

class AdminRequired extends Pipe
{
    public $middlewares = ['AuthMiddleware', 'RequireUserGroupMiddleware'];
    public $attributes = ['required_user_group' => 'admin'];
}
```

And to annotate your controller methods:

```php
use MyNamespace\MyAnnotation;

class Controller
{
    /**
     * @MyAnnotation(value="foobar")
     * @AdminRequired
     */
    public function route()
    {
    }
}
```

## Processing routes using compiler passes

Custom annotations are most useful pared with custom compiler passes.

```php
use inroutephp\inroute\Compiler\CompilerPassInterface;
use inroutephp\inroute\Runtime\RouteInterface;
use MyNamespace\MyAnnotation;

class MyCompilerPass implements CompilerPassInterface
{
    public function processRoute(RouteInterface $route): RouteInterface
    {
        if ($route->hasAnnotation(MyAnnotation::CLASS)) {
            return $route
                ->withAttribute('cool-attribute', $route->getAnnotation(MyAnnotation::CLASS)->value)
                ->withMiddleware(SomeCoolMiddleware::CLASS);
        }

        return $route;
    }
}
```

Each route has a middleware pipeline of its own. In the example above all
routes annotated with `MyAnnotation` will be wrapped in `SomeCoolMiddleware`.
This makes it easy to add custom behaviour to routes at compile time based
on annotations.

The attribute `cool-attribute` can be accessed in middlewares using
`$request->getAttribute('cool-attribute')`.

## Handling dependencies with a DI container

You may have noted that in the example above `SomeCoolMiddleware` was passed
not as an instantiated object but as a class name. The actual object is created
at runtime using a [PSR-11](https://www.php-fig.org/psr/psr-11/) compliant
dependency injection container. The same is true for controller classes.

Create you container as part of your dispatching logic and pass it to the router
using the `setContainer()` method.

<!-- @ignore -->
```php
$container = /* your custom setup */;

$router = new example\HttpRouter;

$router->setContainer($container);

// continue dispatch...
```
