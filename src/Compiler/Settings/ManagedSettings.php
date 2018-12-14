<?php

declare(strict_types = 1);

namespace inroutephp\inroute\Compiler\Settings;

use inroutephp\inroute\Compiler\Exception\UnknownSettingException;

final class ManagedSettings implements SettingsInterface
{
    /**
     * @var SettingsInterface[]
     */
    private $repos;

    public function __construct(SettingsInterface ...$settings)
    {
        $this->repos = $settings;
    }

    public function loadSettings(SettingsInterface $repo): void
    {
        array_unshift($this->repos, $repo);
    }

    public function getSetting(string $name)
    {
        foreach ($this->repos as $repo) {
            if ($repo->hasSetting($name)) {
                return $repo->getSetting($name);
            }
        }

        throw new UnknownSettingException("Unknown setting '$name'");
    }

    public function hasSetting(string $name): bool
    {
        foreach ($this->repos as $repo) {
            if ($repo->hasSetting($name)) {
                return true;
            }
        }

        return false;
    }
}
