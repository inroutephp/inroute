<?php

declare(strict_types = 1);

namespace inroutephp\inroute\Runtime;

use inroutephp\inroute\Annotation\AnnotatedInterface;
use inroutephp\inroute\Exception\LogicException;

final class Route implements RouteInterface
{
    /**
     * @var AnnotatedInterface
     */
    private $annotations;

    /**
     * @var string
     */
    private $name = '';

    /**
     * @var bool
     */
    private $routable = false;

    /**
     * @var string[]
     */
    private $httpMethods = [];

    /**
     * @var string
     */
    private $path = '';

    /**
     * @var string[]
     */
    private $pathTokens = [];

    /**
     * @var string[]
     */
    private $pathDefaults = [];

    /**
     * @var array
     */
    private $attributes = [];

    /**
     * @var string
     */
    private $serviceId = '';

    /**
     * @var string
     */
    private $serviceMethod = '';

    /**
     * @var string[]
     */
    private $middlewareServiceIds = [];

    public function __construct(string $serviceId, string $serviceMethod, AnnotatedInterface $annotations)
    {
        if ($serviceId) {
            $this->name = $serviceId;
            $this->serviceId = $serviceId;
        }

        if ($serviceMethod) {
            $this->name .= ":$serviceMethod";
            $this->serviceMethod = $serviceMethod;
        }

        $this->annotations = $annotations;
    }

    public function __sleep(): array
    {
        return [
            'name',
            'routable',
            'httpMethods',
            'path',
            'pathTokens',
            'pathDefaults',
            'attributes',
            'serviceId',
            'serviceMethod',
            'middlewareServiceIds',
        ];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function withName(string $name): RouteInterface
    {
        $new = clone $this;
        $new->setName($name);
        return $new;
    }

    private function setName(string $name): void
    {
        $this->name = $name;
    }

    public function isRoutable(): bool
    {
        return $this->routable;
    }

    public function withRoutable(bool $routable): RouteInterface
    {
        $new = clone $this;
        $new->setRoutable($routable);
        return $new;
    }

    private function setRoutable(bool $routable): void
    {
        $this->routable = $routable;
    }

    public function getHttpMethods(): array
    {
        return array_values($this->httpMethods);
    }

    public function withHttpMethod(string $httpMethod): RouteInterface
    {
        $new = clone $this;
        $new->setHttpMethods(array_merge($this->httpMethods, [strtoupper($httpMethod)]));
        return $new;
    }

    public function withoutHttpMethod(string $httpMethod): RouteInterface
    {
        $new = clone $this;

        $id = array_search(strtoupper($httpMethod), $this->httpMethods);

        if (false !== $id) {
            $newHttpMethods = $this->httpMethods;
            unset($newHttpMethods[$id]);
            $new->setHttpMethods($newHttpMethods);
        }

        return $new;
    }

    private function setHttpMethods(array $httpMethods): void
    {
        $this->httpMethods = array_change_key_case($httpMethods, CASE_UPPER);
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function withPath(string $path): RouteInterface
    {
        $new = clone $this;
        $new->setPath($path);
        return $new;
    }

    private function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function getPathTokens(): array
    {
        return $this->pathTokens;
    }

    public function getPathDefaults(): array
    {
        return $this->pathDefaults;
    }

    public function withPathToken(string $token, string $regexp, string $default = ''): RouteInterface
    {
        $new = clone $this;
        $new->setPathToken($token, $regexp, $default);
        return $new;
    }

    private function setPathToken(string $token, string $regexp, string $default): void
    {
        if ($regexp) {
            $this->pathTokens[$token] = $regexp;
        }

        if ($default) {
            $this->pathDefaults[$token] = $default;
        }
    }

    private function setPathTokensAndDefaults(array $tokens, array $defaults): void
    {
        $this->pathTokens = $tokens;
        $this->pathDefaults = $defaults;
    }

    public function hasAttribute(string $name): bool
    {
        return isset($this->attributes[$name]);
    }

    public function getAttribute(string $name)
    {
        return $this->attributes[$name] ?? null;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function withAttribute(string $name, $value): RouteInterface
    {
        $new = clone $this;
        $new->setAttribute($name, $value);
        return $new;
    }

    private function setAttribute(string $name, $value): void
    {
        $this->attributes[$name] = $value;
    }

    public function getServiceId(): string
    {
        return $this->serviceId;
    }

    public function withServiceId(string $serviceId): RouteInterface
    {
        $new = clone $this;
        $new->setServiceId($serviceId);
        return $new;
    }

    private function setServiceId(string $serviceId): void
    {
        $this->serviceId = $serviceId;
    }

    public function getServiceMethod(): string
    {
        return $this->serviceMethod;
    }

    public function withServiceMethod(string $serviceMethod): RouteInterface
    {
        $new = clone $this;
        $new->setServiceMethod($serviceMethod);
        return $new;
    }

    private function setServiceMethod(string $serviceMethod): void
    {
        $this->serviceMethod = $serviceMethod;
    }

    public function getMiddlewareServiceIds(): array
    {
        return $this->middlewareServiceIds;
    }

    public function withMiddleware(string $serviceId): RouteInterface
    {
        $new = clone $this;
        $new->setMiddlewareServiceIds(array_merge($this->middlewareServiceIds, [$serviceId]));
        return $new;
    }

    private function setMiddlewareServiceIds(array $serviceIds): void
    {
        $this->middlewareServiceIds = $serviceIds;
    }

    public function hasAnnotation(string $annotationId): bool
    {
        return $this->getWrappedAnnotations()->hasAnnotation($annotationId);
    }

    public function getAnnotation(string $annotationId)
    {
        return $this->getWrappedAnnotations()->getAnnotation($annotationId);
    }

    public function getAnnotations(string $annotationId = ''): array
    {
        return $this->getWrappedAnnotations()->getAnnotations($annotationId);
    }

    private function getWrappedAnnotations(): AnnotatedInterface
    {
        if (!isset($this->annotations)) {
            throw new LogicException('Unserialized route does not contain annotations');
        }

        return $this->annotations;
    }
}
