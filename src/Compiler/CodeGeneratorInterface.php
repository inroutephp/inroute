<?php

namespace inroutephp\inroute\Compiler;

use inroutephp\inroute\Compiler\Settings\SettingsInterface;

interface CodeGeneratorInterface
{
    public function generateRouterCode(SettingsInterface $settings, RouteCollectionInterface $routes): string;
}
