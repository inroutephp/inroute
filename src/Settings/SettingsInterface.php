<?php

namespace inroutephp\inroute\Settings;

interface SettingsInterface
{
    /**
     * @return mixed Loaded setting
     */
    public function getSetting(string $name);

    public function hasSetting(string $name): bool;
}
