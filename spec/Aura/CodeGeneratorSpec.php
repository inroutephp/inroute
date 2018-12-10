<?php

declare(strict_types = 1);

namespace spec\inroutephp\inroute\Aura;

use inroutephp\inroute\Aura\CodeGenerator;
use inroutephp\inroute\Compiler\CodeGeneratorInterface;
use inroutephp\inroute\Compiler\RouteCollection;
use inroutephp\inroute\Annotation\AnnotatedInterface;
use inroutephp\inroute\Runtime\Route;
use inroutephp\inroute\Settings\SettingsInterface;
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

    function it_can_generate_routes(
        SettingsInterface $settings,
        ContainerInterface $container,
        AnnotatedInterface $annotations
    ) {
        $settings->getSetting('router_namespace')->willReturn('spec\\inroutephp\\inroute\\Aura');
        $settings->getSetting('router_classname')->willReturn('HttpRouter');

        $code = $this->generateRouterCode(
            $settings,
            new RouteCollection([new Route('', '', $annotations->getWrappedObject())])
        );

        eval($code->getWrappedObject());

        $router = new HttpRouter($container->getWrappedObject());

        if (!$router) {
            throw new \Exception('Router generation failed');
        }
    }
}
