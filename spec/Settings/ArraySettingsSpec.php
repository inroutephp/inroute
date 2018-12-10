<?php

declare(strict_types = 1);

namespace spec\inroutephp\inroute\Settings;

use inroutephp\inroute\Settings\ArraySettings;
use inroutephp\inroute\Settings\SettingsInterface;
use inroutephp\inroute\Exception\UnknownSettingException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ArraySettingsSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith([]);
        $this->shouldHaveType(ArraySettings::CLASS);
    }

    function it_is_a_settings_repo()
    {
        $this->beConstructedWith([]);
        $this->shouldHaveType(SettingsInterface::CLASS);
    }

    function it_can_check_settings()
    {
        $this->beConstructedWith(['foo' => 'bar']);
        $this->hasSetting('foo')->shouldReturn(true);
        $this->hasSetting('bar')->shouldReturn(false);
    }

    function it_is_case_insensitive()
    {
        $this->beConstructedWith(['foo' => 'bar']);
        $this->hasSetting('FOO')->shouldReturn(true);
    }

    function it_throws_on_unknown_setting()
    {
        $this->beConstructedWith([]);
        $this->shouldThrow(UnknownSettingException::CLASS)->during('getSetting', ['foo']);
    }

    function it_can_read_settings()
    {
        $this->beConstructedWith(['foo' => 'bar']);
        $this->getSetting('foo')->shouldReturn('bar');
    }
}
