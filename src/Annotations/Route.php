<?php

declare(strict_types = 1);

namespace inroutephp\inroute\Annotations;

/**
 * @Annotation
 */
class Route
{
    /**
     * @var string
     */
    public $name;

    /**
     * @Required
     * @var string
     */
    public $method;

    /**
     * @var string
     */
    public $path;

    /**
     * @var array<string, mixed>
     */
    public $attributes = [];
}
