<?php

declare(strict_types = 1);

namespace inroutephp\inroute\Aura;

use inroutephp\inroute\Runtime\UrlGeneratorInterface;
use Aura\Router\Generator;

final class UrlGenerator implements UrlGeneratorInterface
{
    /**
     * @var Generator
     */
    private $generator;

    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    public function generateUrl(string $name, array $values = []): string
    {
        return (string)$this->generator->generate($name, $values);
    }
}
