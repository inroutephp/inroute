<?php

declare(strict_types = 1);

namespace spec\inroutephp\inroute\Compiler;

use inroutephp\inroute\Compiler\Factory;
use inroutephp\inroute\Compiler\CodeGeneratorInterface;
use inroutephp\inroute\Compiler\CompilerInterface;
use inroutephp\inroute\Settings\SettingsInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FactorySpec extends ObjectBehavior
{
    function let(SettingsInterface $settings)
    {
        $this->beConstructedWith($settings);
    }

    function it_is_initializable($settings)
    {
        $settings->hasSetting('container')->willReturn(false);
        $this->shouldHaveType(Factory::CLASS);
    }

    function it_creates_compilers($settings)
    {
        $settings->hasSetting('container')->willReturn(false);
        $settings->hasSetting('bootstrap')->willReturn(false);
        $settings->hasSetting('core_compiler_passes')->willReturn(false);
        $settings->hasSetting('compiler_passes')->willReturn(false);

        $this->createCompiler()->shouldHaveType(CompilerInterface::CLASS);
    }

    function it_creates_code_generators($settings, CodeGeneratorInterface $codeGenerator)
    {
        $settings->hasSetting('container')->willReturn(false);
        $settings->hasSetting('bootstrap')->willReturn(false);
        $settings->hasSetting('code_generator')->willReturn(true);
        $settings->getSetting('code_generator')->willReturn(get_class($codeGenerator->getWrappedObject()));

        $this->createCodeGenerator()->shouldHaveType(CodeGeneratorInterface::CLASS);
    }
}
