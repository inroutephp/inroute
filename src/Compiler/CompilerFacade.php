<?php

declare(strict_types = 1);

namespace inroutephp\inroute\Compiler;

use inroutephp\inroute\Compiler\Settings\ArraySettings;
use inroutephp\inroute\Compiler\Settings\ManagedSettings;
use inroutephp\inroute\Compiler\Settings\SettingsInterface;
use inroutephp\inroute\Compiler\Exception\CompilerException;
use inroutephp\inroute\Runtime\NaiveContainer;
use Psr\Container\ContainerInterface;

final class CompilerFacade
{
    private const DEFAULT_SETTINGS = [
        'bootstrap' => Doctrine\Bootstrap::CLASS,
        'source-dir' => '',
        'source-prefix' => '',
        'source-classes' => [],
        'route-factory' => Doctrine\RouteFactory::CLASS,
        'compiler' => Compiler::CLASS,
        'core-compiler-passes' => [
            Dsl\RouteCompilerPass::CLASS,
            Dsl\BasePathCompilerPass::CLASS,
            Dsl\PipeCompilerPass::CLASS,
        ],
        'compiler-passes' => [],
        'code-generator' => Aura\CodeGenerator::CLASS,
        'target-namespace' => '',
        'target-classname' => 'HttpRouter',
    ];

    public function compileProject(SettingsInterface $settings, RouteCollectionInterface &$routes = null): string
    {
        $settings = new ManagedSettings($settings, new ArraySettings(self::DEFAULT_SETTINGS));

        $container = new NaiveContainer;

        if ($settings->hasSetting('container')) {
            /** @var ContainerInterface */
            $container = $this->build('container', ContainerInterface::CLASS, $container, $settings);
        }

        if ($container->has($settings->getSetting('bootstrap'))) {
            /** @var BootstrapInterface */
            $bootstrap = $this->build('bootstrap', BootstrapInterface::CLASS, $container, $settings);
            $bootstrap->bootstrap($settings);
        }

        /** @var RouteFactoryInterface */
        $routeFactory = $this->build('route-factory', RouteFactoryInterface::CLASS, $container, $settings);

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

        /** @var CompilerInterface */
        $compiler = $this->build('compiler', CompilerInterface::CLASS, $container, $settings);

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

        /** @var CodeGeneratorInterface */
        $generator = $this->build('code-generator', CodeGeneratorInterface::CLASS, $container, $settings);

        return $generator->generateRouterCode($settings, $routes);
    }

    /** @return object */
    private function build(
        string $setting,
        string $requiredClass,
        ContainerInterface $container,
        SettingsInterface $settings
    ) {
        $obj = $container->get($settings->getSetting($setting));

        if (!$obj instanceof $requiredClass) {
            throw new CompilerException(
                "Service '{$settings->getSetting($setting)}' must implement $requiredClass"
            );
        }

        return $obj;
    }
}
