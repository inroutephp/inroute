<?php

namespace inroutephp\inroute\Compiler\Settings;

interface SettingsInterface
{
    /** @return mixed */
    public function getSetting(string $name);

    public function hasSetting(string $name): bool;
}
