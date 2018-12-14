<?php

declare(strict_types = 1);

namespace inroutephp\inroute\Annotations;

/**
 * @Annotation
 * */
class Pipe
{
    /**
     * @var array
     */
    public $middlewares = [];

    /**
     * @var array
     */
    public $attributes = [];
}
