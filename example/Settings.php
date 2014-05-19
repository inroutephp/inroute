<?php
namespace inroute\example;

use inroute\Settings\CompileSettingsInterface;

class Settings implements CompileSettingsInterface
{
    public function getRootPath()
    {
        return '/example';
    }

    public function getPlugins()
    {
        return [
            new HtmlPlugin
        ];
    }
}
