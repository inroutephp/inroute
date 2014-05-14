<?php
namespace inroute\Settings;

class DefaultCompileSettings implements CompileSettingsInterface
{
    use DefaultCompileSettingsTrait;
}

class DefaultCompileSettingsTraitTest extends \PHPUnit_Framework_TestCase
{
    public function testTraitImplementation()
    {
        $settings = new DefaultCompileSettings;
        $this->assertEmpty($settings->getRootPath());
        $this->assertEmpty($settings->getPlugins());
    }
}
