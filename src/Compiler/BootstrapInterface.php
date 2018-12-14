<?php

namespace inroutephp\inroute\Compiler;

use inroutephp\inroute\Compiler\Settings\SettingsInterface;

interface BootstrapInterface
{
    /**
     * Perform custom actions to bootstrap the compilation process
     */
    public function bootstrap(SettingsInterface $settings): void;
}
