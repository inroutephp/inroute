<?php

declare(strict_types = 1);

namespace inroutephp\inroute\Compiler;

use inroutephp\inroute\Runtime\NaiveContainer;
use inroutephp\inroute\Settings\ArraySettings;
use inroutephp\inroute\Settings\ManagedSettings;
use inroutephp\inroute\Settings\SettingsInterface;
use inroutephp\inroute\Exception\LogicException;
use Psr\Container\ContainerInterface;

final class Factory
{
    private const DEFAULT_SETTINGS = [
        'bootstrap' => BootstrapInterface::CLASS,
        'core_compiler_passes' => [],
        'compiler_passes' => [],
        'code_generator' => CodeGeneratorInterface::CLASS,
    ];

    /**
     * @var SettingsInterface
     */
    private $settings;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(SettingsInterface $settings)
    {
        $this->settings = new ManagedSettings($settings, new ArraySettings(self::DEFAULT_SETTINGS));

        $this->container = (new NaiveContainer);

        if ($this->settings->hasSetting('container')) {
            $this->container = $this->container->get(
                $this->settings->getSetting('container')
            );

            if (!$this->container instanceof ContainerInterface) {
                throw new LogicException(
                    "Service '{$this->settings->getSetting('container')}' must implement ContainerInterface"
                );
            }
        }

        if ($this->container->has($this->settings->getSetting('bootstrap'))) {
            $bootstrap = $this->container->get($this->settings->getSetting('bootstrap'));

            if (!$bootstrap instanceof BootstrapInterface) {
                throw new LogicException(
                    "Service '{$this->settings->getSetting('bootstrap')}' must implement BootstrapInterface"
                );
            }

            $bootstrap->bootstrap($this->settings);
        }
    }

    public function createCompiler(): CompilerInterface
    {
        $compiler = new Compiler;

        foreach (['core_compiler_passes', 'compiler_passes'] as $settingName) {
            foreach ((array)$this->settings->getSetting($settingName) as $serviceName) {
                $this->addCompilerPassTo($serviceName, $compiler);
            }
        }

        return $compiler;
    }

    public function createCodeGenerator(): CodeGeneratorInterface
    {
        $generator = $this->container->get($this->settings->getSetting('code_generator'));

        if (!$generator instanceof CodeGeneratorInterface) {
            throw new LogicException(
                "Service '{$this->settings->getSetting('code_generator')}' must implement CodeGeneratorInterface"
            );
        }

        return $generator;
    }

    private function addCompilerPassTo(string $serviceName, CompilerInterface $compiler): void
    {
        $compilerPass = $this->container->get($serviceName);

        if (!$compilerPass instanceof CompilerPassInterface) {
            throw new LogicException("Service '$serviceName' must implement CompilerPassInterface");
        }

        $compiler->addCompilerPass($compilerPass);
    }
}
