<?php

declare(strict_types = 1);

namespace inroutephp\inroute\Runtime;

final class Environment implements EnvironmentInterface
{
    /**
     * @var RouteInterface
     */
    private $route;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(RouteInterface $route, UrlGeneratorInterface $urlGenerator)
    {
        $this->route = $route;
        $this->urlGenerator = $urlGenerator;
    }

    public function getRoute(): RouteInterface
    {
        return $this->route;
    }

    public function getUrlGenerator(): UrlGeneratorInterface
    {
        return $this->urlGenerator;
    }
}
