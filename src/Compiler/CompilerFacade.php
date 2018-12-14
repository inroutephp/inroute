<?php

declare(strict_types = 1);

namespace inroutephp\inroute\Compiler;

use inroutephp\inroute\Compiler\Aura\CodeGenerator;
use inroutephp\inroute\Compiler\Doctrine\Bootstrap;
use inroutephp\inroute\Compiler\Doctrine\RouteFactory;
use inroutephp\inroute\Compiler\Dsl\RouteCompilerPass;
use inroutephp\inroute\Compiler\Settings\ArraySettings;
use inroutephp\inroute\Compiler\Settings\ManagedSettings;
use inroutephp\inroute\Compiler\Settings\SettingsInterface;
use inroutephp\inroute\Runtime\NaiveContainer;
use inroutephp\inroute\Compiler\Exception\CompilerException;
use Psr\Container\ContainerInterface;

final class CompilerFacade
{
    private const DEFAULT_SETTINGS = [
        'bootstrap' => Bootstrap::CLASS,
        'source-dir' => '',
        'source-prefix' => '',
        'source-classes' => [],
        'core-compiler-passes' => [RouteCompilerPass::CLASS],
        'compiler-passes' => [],
        'code-generator' => CodeGenerator::CLASS,
        'target-namespace' => '',
        'target-classname' => 'HttpRouter',
    ];

    public function compileProject(SettingsInterface $settings, RouteCollectionInterface &$routes = null): string
    {
        $settings = new ManagedSettings($settings, new ArraySettings(self::DEFAULT_SETTINGS));

        $container = new NaiveContainer;

        if ($settings->hasSetting('container')) {
            $container = $container->get($settings->getSetting('container'));

            if (!$container instanceof ContainerInterface) {
                throw new CompilerException(
                    "Service '{$settings->getSetting('container')}' must implement ContainerInterface"
                );
            }
        }

        if ($container->has($settings->getSetting('bootstrap'))) {
            $bootstrap = $container->get($settings->getSetting('bootstrap'));

            if (!$bootstrap instanceof BootstrapInterface) {
                throw new CompilerException(
                    "Service '{$settings->getSetting('bootstrap')}' must implement BootstrapInterface"
                );
            }

            $bootstrap->bootstrap($settings);
        }

        $routeFactory = new RouteFactory;

        /** @var RouteCollectionInterface[] */
        $collections = [];

        foreach ((array)$settings->getSetting('source-classes') as $classname) {
            $collections[] = $routeFactory->createRoutesFrom($classname);
        }

        if ($settings->getSetting('source-dir')) {
            $classFinder = new Psr4ClassFinder(
                $settings->getSetting('source-dir'),
                $settings->getSetting('source-prefix')
            );

            foreach ($classFinder as $classname) {
                $collections[] = $routeFactory->createRoutesFrom((string)$classname);
            }
        }

        $compiler = new Compiler;

        foreach (['core-compiler-passes', 'compiler-passes'] as $settingName) {
            foreach ((array)$settings->getSetting($settingName) as $serviceName) {
                $compilerPass = $container->get($serviceName);

                if (!$compilerPass instanceof CompilerPassInterface) {
                    throw new CompilerException("Service '$serviceName' must implement CompilerPassInterface");
                }

                $compiler->addCompilerPass($compilerPass);
            }
        }

        $routes = $compiler->compile(...$collections);

        $generator = $container->get($settings->getSetting('code-generator'));

        if (!$generator instanceof CodeGeneratorInterface) {
            throw new CompilerException(
                "Service '{$settings->getSetting('code-generator')}' must implement CodeGeneratorInterface"
            );
        }

        return $generator->generateRouterCode($settings, $routes);
    }
}
