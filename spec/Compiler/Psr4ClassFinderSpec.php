<?php

declare(strict_types = 1);

namespace spec\inroutephp\inroute\Compiler;

use inroutephp\inroute\Compiler\Psr4ClassFinder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class Psr4ClassFinderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith('', '');
        $this->shouldHaveType(Psr4ClassFinder::CLASS);
    }

    function it_finds_classes()
    {
        $this->beConstructedWith(__DIR__ . '/../../src/Compiler/Settings', 'inroutephp\inroute\Compiler\Settings');
        $this->getIterator()->shouldContain(\inroutephp\inroute\Compiler\Settings\SettingsInterface::CLASS);
    }

    function it_enters_subdirs()
    {
        $this->beConstructedWith(__DIR__ . '/../../src', 'inroutephp\inroute');
        $this->getIterator()->shouldContain(\inroutephp\inroute\Compiler\Settings\SettingsInterface::CLASS);
    }

    function it_handles_trailing_path_slashes()
    {
        $this->beConstructedWith(__DIR__ . '/../../src/Compiler/', 'inroutephp\inroute\Compiler');
        $this->getIterator()->shouldContain(\inroutephp\inroute\Compiler\Settings\SettingsInterface::CLASS);
    }

    function it_handles_trailing_prefix_backslashes()
    {
        $this->beConstructedWith(__DIR__ . '/../../src/Compiler/', 'inroutephp\inroute\Compiler\\');
        $this->getIterator()->shouldContain(\inroutephp\inroute\Compiler\Settings\SettingsInterface::CLASS);
    }
}
