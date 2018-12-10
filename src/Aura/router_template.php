
declare(strict_types = 1);

<?php if ($settings->getSetting('router_namespace')) { ?>
namespace <?= $settings->getSetting('router_namespace') ?>;
<?php } ?>

/**
 * NOTE: This file was auto-generated by inroute <?= date('Y-m-d') ?> and should not be edited directly
 */
final class <?= $settings->getSetting('router_classname') ?> implements \inroutephp\inroute\Runtime\HttpRouterInterface
{
    use \inroutephp\inroute\Aura\HttpRouterTrait;

    protected function loadRoutes(\Aura\Router\Map $map): void
    {
        $mapper = new \inroutephp\inroute\Aura\RouteMapper($map);

<?php foreach ($exportedRoutes as $route) { ?>
$mapper->mapRoute(<?= $route ?>);
<?php } ?>
    }
}