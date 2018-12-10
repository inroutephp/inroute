<?php

declare(strict_types = 1);

namespace inroutephp\inroute\Compiler;

use inroutephp\inroute\Annotation\LoaderBootstrap;
use inroutephp\inroute\Annotation\RouteCompilerPass;
use inroutephp\inroute\Annotation\RouteFactory;
use inroutephp\inroute\Aura\CodeGenerator;
use inroutephp\inroute\Runtime\NaiveContainer;
use inroutephp\inroute\Settings\ArraySettings;
use inroutephp\inroute\Settings\ManagedSettings;
use inroutephp\inroute\Settings\SettingsInterface;
use inroutephp\inroute\Exception\LogicException;
use Psr\Container\ContainerInterface;

final class CompilerFacade
{
    private const DEFAULT_SETTINGS = [
        'bootstrap' => LoaderBootstrap::CLASS,
        'source_dir' => '',
        'source_prefix' => '',
        'controllers' => [],
        'core_compiler_passes' => [RouteCompilerPass::CLASS],
        'compiler_passes' => [],
        'code_generator' => CodeGenerator::CLASS,
        'router_namespace' => '',
        'router_classname' => 'HttpRouter',
    ];

    public function compileProject(SettingsInterface $settings): string
    {
        $settings = new ManagedSettings($settings, new ArraySettings(self::DEFAULT_SETTINGS));

        $container = new NaiveContainer;

        if ($settings->hasSetting('container')) {
            $container = $container->get($settings->getSetting('container'));

            if (!$container instanceof ContainerInterface) {
                throw new LogicException(
                    "Service '{$settings->getSetting('container')}' must implement ContainerInterface"
                );
            }
        }

        if ($container->has($settings->getSetting('bootstrap'))) {
            $bootstrap = $container->get($settings->getSetting('bootstrap'));

            if (!$bootstrap instanceof BootstrapInterface) {
                throw new LogicException(
                    "Service '{$settings->getSetting('bootstrap')}' must implement BootstrapInterface"
                );
            }

            $bootstrap->bootstrap($settings);
        }

        $routeFactory = new RouteFactory;

        /** @var RouteCollectionInterface[] */
        $collections = [];

        foreach ((array)$settings->getSetting('controllers') as $classname) {
            $collections[] = $routeFactory->createRoutesFrom($classname);
        }

        if ($settings->getSetting('source_dir')) {
            $classFinder = new Psr4ClassFinder(
                $settings->getSetting('source_dir'),
                $settings->getSetting('source_prefix')
            );

            foreach ($classFinder as $classname) {
                $collections[] = $routeFactory->createRoutesFrom($classname);
            }
        }

        $compiler = new Compiler;

        foreach (['core_compiler_passes', 'compiler_passes'] as $settingName) {
            foreach ((array)$settings->getSetting($settingName) as $serviceName) {
                $compilerPass = $container->get($serviceName);

                if (!$compilerPass instanceof CompilerPassInterface) {
                    throw new LogicException("Service '$serviceName' must implement CompilerPassInterface");
                }

                $compiler->addCompilerPass($compilerPass);
            }
        }

        $routes = $compiler->compile(...$collections);

        $generator = $container->get($settings->getSetting('code_generator'));

        if (!$generator instanceof CodeGeneratorInterface) {
            throw new LogicException(
                "Service '{$settings->getSetting('code_generator')}' must implement CodeGeneratorInterface"
            );
        }

        return $generator->generateRouterCode($settings, $routes);
    }
}