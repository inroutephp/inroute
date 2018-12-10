<?php

declare(strict_types = 1);

namespace spec\inroutephp\inroute\Settings;

use inroutephp\inroute\Settings\ManagedSettings;
use inroutephp\inroute\Settings\SettingsInterface;
use inroutephp\inroute\Exception\UnknownSettingException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ManagedSettingsSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ManagedSettings::CLASS);
    }

    function it_is_a_settings_repo()
    {
        $this->shouldHaveType(SettingsInterface::CLASS);
    }

    function it_can_check_settings(SettingsInterface $settings)
    {
        $settings->hasSetting('foo')->willReturn(true);
        $settings->hasSetting('bar')->willReturn(false);
        $this->loadSettings($settings);
        $this->hasSetting('foo')->shouldReturn(true);
        $this->hasSetting('bar')->shouldReturn(false);
    }

    function it_throws_on_unknown_setting(SettingsInterface $settings)
    {
        $settings->hasSetting('foo')->willReturn(false);
        $this->loadSettings($settings);
        $this->shouldThrow(UnknownSettingException::CLASS)->during('getSetting', ['foo']);
    }

    function it_can_read_settings(SettingsInterface $settings)
    {
        $settings->hasSetting('foo')->willReturn(true);
        $settings->getSetting('foo')->willReturn('bar');
        $this->loadSettings($settings);
        $this->getSetting('foo')->shouldReturn('bar');
    }

    function it_reads_last_loaded_setting(SettingsInterface $settingsA, SettingsInterface $settingsB)
    {
        $settingsA->hasSetting('foo')->willReturn(true);
        $settingsA->getSetting('foo')->willReturn('A');
        $this->loadSettings($settingsA);
        $settingsB->hasSetting('foo')->willReturn(true);
        $settingsB->getSetting('foo')->willReturn('B');
        $this->loadSettings($settingsB);
        $this->getSetting('foo')->shouldReturn('B');
    }
}
