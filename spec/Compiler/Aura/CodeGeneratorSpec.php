<?php

declare(strict_types = 1);

namespace spec\inroutephp\inroute\Compiler\Aura;

use inroutephp\inroute\Compiler\Aura\CodeGenerator;
use inroutephp\inroute\Compiler\CodeGeneratorInterface;
use inroutephp\inroute\Compiler\RouteCollection;
use inroutephp\inroute\Compiler\Settings\SettingsInterface;
use inroutephp\inroute\Runtime\Route;
use Psr\Container\ContainerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CodeGeneratorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(CodeGenerator::CLASS);
    }

    function it_is_a_code_generator()
    {
        $this->shouldHaveType(CodeGeneratorInterface::CLASS);
    }

    function it_can_generate_routes(SettingsInterface $settings, ContainerInterface $container)
    {
        $settings->getSetting('target-namespace')->willReturn('spec\\inroutephp\\inroute\\Compiler\\Aura');
        $settings->getSetting('target-classname')->willReturn('HttpRouter');

        $code = $this->generateRouterCode(
            $settings,
            new RouteCollection([new Route('', '', [])])
        );

        eval($code->getWrappedObject());

        $router = new HttpRouter($container->getWrappedObject());

        if (!$router) {
            throw new \Exception('Router generation failed');
        }
    }
}
