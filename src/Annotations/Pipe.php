<?php

declare(strict_types = 1);

namespace inroutephp\inroute\Annotations;

/**
 * @Annotation
 */
class Pipe
{
    /** @var array<string> */
    public $middlewares = [];

    /** @var array<string, mixed> */
    public $attributes = [];
}
