<?php
namespace inroute\Settings;

class ComposerJsonParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParseAutoload()
    {
        $pathToComposerJson = __DIR__.'/../../composer.json';
        $parser = ComposerJsonParser::createFromFile($pathToComposerJson);
        $this->assertArrayHasKey('src/', $parser->getPaths());
    }

    public function testParseNonJsonFile()
    {
        $parser = ComposerJsonParser::createFromFile(__FILE__);
        $this->assertEmpty($parser->getPaths());
    }
}
