<?php

declare(strict_types = 1);

namespace inroutephp\inroute\Runtime;

final class Route implements RouteInterface
{
    /**
     * @var array<string, mixed>
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
     * @var array<string>
     */
    private $httpMethods = [];

    /**
     * @var string
     */
    private $path = '';

    /**
     * @var array<string, mixed>
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
     * @var array<string>
     */
    private $middlewareServiceIds = [];

    /**
     * @param array<object> $annotations
     */
    public function __construct(string $serviceId, string $serviceMethod, array $annotations)
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

    /**
     * @param array<string> $httpMethods
     */
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

    /**
     * @param mixed $value
     */
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

    /**
     * @param array<string> $serviceIds
     */
    private function setMiddlewareServiceIds(array $serviceIds): void
    {
        $this->middlewareServiceIds = $serviceIds;
    }

    public function hasAnnotation(string $annotationId): bool
    {
        foreach ($this->annotations as $annotation) {
            if ($annotation instanceof $annotationId) {
                return true;
            }
        }

        return false;
    }

    public function getAnnotation(string $annotationId)
    {
        foreach ($this->annotations as $annotation) {
            if ($annotation instanceof $annotationId) {
                return $annotation;
            }
        }

        return null;
    }

    public function getAnnotations(string $annotationId = ''): array
    {
        if (!$annotationId) {
            return $this->annotations;
        }

        $annotations = [];

        foreach ($this->annotations as $annotation) {
            if ($annotation instanceof $annotationId) {
                $annotations[] = $annotation;
            }
        }

        return $annotations;
    }
}
