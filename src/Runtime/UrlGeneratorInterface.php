<?php

namespace inroutephp\inroute\Runtime;

interface UrlGeneratorInterface
{
    /**
     * @param array<string, string> $values
     */
    public function generateUrl(string $name, array $values = []): string;
}
