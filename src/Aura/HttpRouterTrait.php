<?php

declare(strict_types = 1);

namespace inroutephp\inroute\Aura;

use inroutephp\inroute\Exception\RouteNotFoundException;
use inroutephp\inroute\Exception\MethodNotAllowedException;
use inroutephp\inroute\Runtime\DispatchingMiddleware;
use inroutephp\inroute\Runtime\Environment;
use inroutephp\inroute\Runtime\NaiveContainer;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Aura\Router\RouterContainer;
use Aura\Router\Map;
use mindplay\middleman\Dispatcher;
use mindplay\middleman\ContainerResolver;

trait HttpRouterTrait
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct()
    {
        $this->container = new NaiveContainer;
    }

    abstract protected function loadRoutes(Map $map): void;

    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $router = new RouterContainer;

        $this->loadRoutes($router->getMap());

        $matcher = $router->getMatcher();

        $match = $matcher->match($request);

        if (!$match) {
            $failedRoute = $matcher->getFailedRoute();

            if ($failedRoute && $failedRoute->failedRule == 'Aura\Router\Rule\Allows') {
                if ($this->container->has(ResponseFactoryInterface::CLASS)) {
                    return $this->container->get(ResponseFactoryInterface::CLASS)
                        ->createResponse(405)
                        ->withHeader('Allow', implode(', ', $failedRoute->allows));
                }

                throw new MethodNotAllowedException($request, ['allows' => $failedRoute->allows]);
            }

            if ($this->container->has(ResponseFactoryInterface::CLASS)) {
                return $this->container->get(ResponseFactoryInterface::CLASS)->createResponse(404);
            }

            throw new RouteNotFoundException($request);
        }

        /** @var RouteInterface $route */
        $route = $match->handler;

        foreach ($route->getAttributes() as $name => $val) {
            $request = $request->withAttribute($name, $val);
        }

        foreach ($match->attributes as $name => $val) {
            $request = $request->withAttribute($name, $val);
        }

        $middlewares = $route->getMiddlewareServiceIds();

        $middlewares[] = new DispatchingMiddleware(
            [$this->container->get($route->getServiceId()), $route->getServiceMethod()],
            new Environment($route, new UrlGenerator($router->getGenerator()))
        );

        return (new Dispatcher($middlewares, new ContainerResolver($this->container)))->dispatch($request);
    }
}
