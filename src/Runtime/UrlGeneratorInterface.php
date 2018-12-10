<?php

namespace inroutephp\inroute\Runtime;

interface UrlGeneratorInterface
{
    public function generateUrl(string $name, array $values = []): string;
}
