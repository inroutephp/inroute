<?php

declare(strict_types = 1);

namespace inroutephp\inroute\Annotation;

final class AnnotatedObject implements AnnotatedInterface
{
    /**
     * @var array
     */
    private $annotations;

    public function __construct(array $annotations = [])
    {
        $this->annotations = $annotations;
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
