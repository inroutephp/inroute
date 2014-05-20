<?php
namespace inroute\example;

use inroute\Settings\SettingsInterface;

class Settings implements SettingsInterface
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
