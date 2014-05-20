<?php
namespace inroute\Settings;

class ComposerWrapperTest extends \PHPUnit_Framework_TestCase
{
    public function testParseAutoload()
    {
        $pathToComposerJson = __DIR__.'/../../composer.json';
        $parser = ComposerWrapper::createFromFile($pathToComposerJson);
        $this->assertArrayHasKey('src/', $parser->getPaths());
    }

    public function testParseNonJsonFile()
    {
        $parser = ComposerWrapper::createFromFile(__FILE__);
        $this->assertEmpty($parser->getPaths());
    }
}
