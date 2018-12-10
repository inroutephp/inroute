<?php

declare(strict_types = 1);

namespace inroutephp\inroute\Annotation;

use inroutephp\inroute\Compiler\BootstrapInterface;
use inroutephp\inroute\Settings\SettingsInterface;
use Doctrine\Common\Annotations\AnnotationRegistry;

/**
 * Register a global doctrine annotaion loader using class_exists
 */
final class LoaderBootstrap implements BootstrapInterface
{
    public function bootstrap(SettingsInterface $settings): void
    {
        AnnotationRegistry::registerLoader('class_exists');
    }
}
