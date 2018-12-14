<?php

declare(strict_types = 1);

namespace inroutephp\inroute\Compiler\Doctrine;

use inroutephp\inroute\Compiler\BootstrapInterface;
use inroutephp\inroute\Compiler\Settings\SettingsInterface;
use Doctrine\Common\Annotations\AnnotationRegistry;

/**
 * Register a global doctrine annotaion loader using class_exists
 */
final class Bootstrap implements BootstrapInterface
{
    public function bootstrap(SettingsInterface $settings): void
    {
        AnnotationRegistry::registerLoader('class_exists');
    }
}
