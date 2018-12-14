<?php

declare(strict_types = 1);

namespace inroutephp\inroute\Annotations;

/**
 * @Annotation
 * @Target({"METHOD"})
 * */
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
     * @Required
     * @var string
     */
    public $path;

    /**
     * @var array
     */
    public $attributes;
}
