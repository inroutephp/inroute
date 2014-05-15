<?php
namespace inroute\example;

use inroute\Settings\CompileSettingsInterface;
use inroute\Settings\DefaultCompileSettingsTrait;

class Settings implements CompileSettingsInterface
{
    use DefaultCompileSettingsTrait;
}
