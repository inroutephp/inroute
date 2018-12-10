# ![Inroute](res/logo.png "Inroute")

[![Packagist Version](https://img.shields.io/packagist/v/inroutephp/inroute.svg?style=flat-square)](https://packagist.org/packages/inroutephp/inroute)
[![Build Status](https://img.shields.io/travis/inroutephp/inroute/master.svg?style=flat-square)](https://travis-ci.org/inroutephp/inroute)
[![Quality Score](https://img.shields.io/scrutinizer/g/inroutephp/inroute.svg?style=flat-square)](https://scrutinizer-ci.com/g/inroutephp/inroute)

Generate http routing and dispatching middleware from docblock annotations.

Inroute is a code generator. It scans your source tree for annotated routes and
generates a [PSR-15](https://www.php-fig.org/psr/psr-15/) compliant http routing
middleware. In addition all routes have a middleware pipeline of their own,
making it easy to add behaviour at compile time based on cutom annotations.

* See the [example-app](https://github.com/inroutephp/example-app) for a
  complete example.
* See [console](https://github.com/inroutephp/console) for a compiler tool for
  the command line.

## Installation

```shell
composer require inroutephp/inroute:^1.0@beta
```

## Table of contents

1. [Writing routes](#writing routes)
1. [Compiling](#compiling)
1. [Dispatching](#dispatching)
1. [Generating route paths](#generating-route-paths)
1. [Creating custom annotations](#creating-custom-annotations)
1. [Processing routes using compiler passes](#processing-routes-using-compiler-passes)
1. [Handling dependencies with a DI container](#handling-dependencies-with-a-di-container)

## Writing routes

Routes are annotated using a simple `@Route` annotation, are called with
a [PSR-7](https://www.php-fig.org/psr/psr-7/) request object and inroute
[environment](src/Runtime\EnvironmentInterface.php) and are expected to
return a PSR-7 response.

<!-- @example UserController -->
```php
use inroutephp\inroute\Annotation\Route;
use inroutephp\inroute\Runtime\EnvironmentInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\TextResponse;

class UserController
{
    /**
     * @Route(
     *     method="GET",
     *     path="/users/{name}",
     *     name="getUser",
     *     attributes={
     *         "custom-attribute": "value"
     *     }
     * )
     */
    public function getUser(ServerRequestInterface $request, EnvironmentInterface $environment): ResponseInterface
    {
        return new TextResponse(
            // the name attribute from the request path
            $request->getAttribute('name')

            // the custom route attribute
            . $environment->getRoute()->getAttribute('custom-attribute')
        );
    }
}
```

* The `method` and `path` values are self explanatory.
* A route `name` is optional, and defaults to `class:method` (in the example
  `UserController:getUser`).
* `Attributes` are custom values that can be accessed at runtime through the
  environment as seen above.
* Note that the use of zend diactoros as a psr-7 response implementation is
  used in this example, you may of courseuse  another psr-7 implementation.

## Compiling

The recommended way of building a project is by using the
[console](https://github.com/inroutephp/console) build tool. Compiling from
pure php involves setting up the compiler something like the following.

<!--
    @example Router
    @include UserController
-->
```php
use inroutephp\inroute\Annotation\LoaderBootstrap;
use inroutephp\inroute\Annotation\RouteCompilerPass;
use inroutephp\inroute\Annotation\RouteFactory;
use inroutephp\inroute\Aura\CodeGenerator;
use inroutephp\inroute\Compiler\Factory;
use inroutephp\inroute\Settings\ArraySettings;

$settings = new ArraySettings([
    'bootstrap' => LoaderBootstrap::CLASS,
    'core_compiler_passes' => [RouteCompilerPass::CLASS],
    'router_namespace' => 'example',
    'router_classname' => 'HttpRouter',
]);

$compiler = (new Factory($settings))->createCompiler();

$routes = $compiler->compile((new RouteFactory)->createRoutesFrom(UserController::CLASS));

$code = (new CodeGenerator)->generateRouterCode($settings, $routes);

eval($code);

$router = new example\HttpRouter;
```

### Creating OpenApi apps

Instead of using the `@Route` annotation inroute is able to build OpenApi
projects annotated with [swagger-php](https://github.com/zircote/swagger-php)
annotations. To build OpenApi apps replace `RouteCompilerPass` above with
`inroutephp\inroute\OpenApi\OpenApiCompilerPass`.

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

// fakeing a GET request
$request = (new ServerRequestFactory)->createServerRequest('GET', '/users/foo');

// creating and dispatching a simple middleware pipeline
$response = (new Dispatcher([$router]))->dispatch($request);

// emitting the response
(new SapiEmitter)->emit($response);
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

Inroute use [doctrine](https://github.com/doctrine/annotations) to read
annotations. Creating custom annotations is as easy as

```php
namespace MyNamespace;

/** @Annotation */
class MyAnnotation
{
    public $value;
}
```

And you may then annotate your controller methods

```php
use MyNamespace\MyAnnotation;

class Controller
{
    /**
     * @MyAnnotation(value="foobar")
     */
    public function route()
    {
    }
}
```

## Processing routes using compiler passes

Custom annotations are most useful pared with custom compiler passes

```php
use inroutephp\inroute\Compiler\CompilerPassInterface;
use inroutephp\inroute\Runtime\RouteInterface;
use MyNamespace\MyAnnotation;

class MyCompilerPass implements CompilerPassInterface
{
    public function processRoute(RouteInterface $route): RouteInterface
    {
        if ($route->hasAnnotation(MyAnnotation::CLASS)) {
            return $route->withMiddleware(SomeCoolMiddleware::CLASS);
        }

        return $route;
    }
}
```

Each route has a middleware pipeline of its own. In the example above all
routes annotated with `MyAnnotation` will be wrapped in `SomeCoolMiddleware`.
This makes it easy to add custom behaviour to routes at compile time based
on annotations.

## Handling dependencies with a DI container

You may have noted that in the example above `SomeCoolMiddleware` was passed
not as an instantiated object but as a class name. The actual object is created
at runtime using a [PSR-11](https://www.php-fig.org/psr/psr-11/) dependency
injection container. The same is true for controller classes.

Create you container as part of your dispatching logic and pass it to the router
using the `setContainer()` method.

<!-- @ignore -->
```php
$container = /* your custom setup */;

$router = new example\HttpRouter;

$router->setContainer($container);

// continue dispatch...
```
