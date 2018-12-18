<?php

declare(strict_types = 1);

namespace inroutephp\inroute\Compiler\Doctrine;

use inroutephp\inroute\Compiler\BootstrapInterface;
use inroutephp\inroute\Compiler\Settings\SettingsInterface;
use inroutephp\inroute\Compiler\Exception\CompilerException;
use Doctrine\Common\Annotations\AnnotationRegistry;

/**
 * Register a global doctrine annotaion loader using class_exists
 */
final class Bootstrap implements BootstrapInterface
{
    public function bootstrap(SettingsInterface $settings): void
    {
        if (!class_exists(AnnotationRegistry::CLASS)) {
            throw new CompilerException('Doctrine annotations not loaded. Require doctrine/annotations^1.6');
        }

        AnnotationRegistry::registerLoader('class_exists');
    }
}
