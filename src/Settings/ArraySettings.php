<?php

declare(strict_types = 1);

namespace inroutephp\inroute\Settings;

use inroutephp\inroute\Exception\UnknownSettingException;

final class ArraySettings implements SettingsInterface
{
    /**
     * @var array
     */
    private $settings;

    public function __construct(array $settings)
    {
        $this->settings = array_change_key_case($settings, CASE_UPPER);
    }

    public function getSetting(string $name)
    {
        if (!$this->hasSetting($name)) {
            throw new UnknownSettingException("Unknown setting '$name'");
        }

        return $this->settings[strtoupper($name)];
    }

    public function hasSetting(string $name): bool
    {
        return isset($this->settings[strtoupper($name)]);
    }
}
