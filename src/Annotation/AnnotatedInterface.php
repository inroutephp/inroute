<?php

namespace inroutephp\inroute\Annotation;

interface AnnotatedInterface
{
    /**
     * Check if annotation is present
     */
    public function hasAnnotation(string $annotationId): bool;

    /**
     * Get first instance of annotation id
     */
    public function getAnnotation(string $annotationId);

    /**
     * Get set of annotations, possibly filtered by id
     */
    public function getAnnotations(string $annotationId = ''): array;
}
