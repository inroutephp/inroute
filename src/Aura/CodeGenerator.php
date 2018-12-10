<?php

declare(strict_types = 1);

namespace inroutephp\inroute\Aura;

use inroutephp\inroute\Compiler\CodeGeneratorInterface;
use inroutephp\inroute\Compiler\RouteCollectionInterface;
use inroutephp\inroute\Settings\SettingsInterface;
use Symfony\Component\VarExporter\VarExporter;

final class CodeGenerator implements CodeGeneratorInterface
{
    private const TEMPLATE_PATH = __DIR__ . '/router_template.php';

    public function generateRouterCode(SettingsInterface $settings, RouteCollectionInterface $routes): string
    {
        $exportedRoutes = [];

        foreach ($routes->getRoutes() as $route) {
            $exportedRoutes[] = VarExporter::export($route);
        }

        ob_start();
        require self::TEMPLATE_PATH;
        $code = ob_get_contents();
        ob_end_clean();

        return (string)$code;
    }
}
