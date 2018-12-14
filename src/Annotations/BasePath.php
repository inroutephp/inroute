<?php

declare(strict_types = 1);

namespace inroutephp\inroute\Annotations;

/**
 * @Annotation
 * */
class BasePath
{
    /**
     * @Required
     * @var string
     */
    public $path;
}
