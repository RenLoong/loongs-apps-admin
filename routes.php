<?php

declare(strict_types=1);

/**
 * Admin app bootstrap + routes.
 *
 * Console / system management moved from Uuims. Auth (OAuth / login / tokens)
 * stays in Uuims; AdminAuthMiddleware calls $rpc->call('uuims', 'Auth.check', …).
 *
 * Module routes (Modules/<Module>/routes.php) are discovered by the framework;
 * app-wide extras live in apps/Admin/routes/*.php (loaded here, sorted).
 */

use App\Admin\Render\PageService;
use App\Admin\Services\MenusService;
use App\Admin\Services\PermissionService;
use App\Admin\Support\I18n;
use App\Admin\Support\MenuRegistry;
use Loongs\Cache\CacheManager;
use Loongs\Config\Repository;
use Loongs\Language\Integration\Render\RenderTranslator;
use Loongs\Language\Translator;
use Loongs\Render\PageRegistry;
use Loongs\Rpc\Client\RpcClientInterface;
use Loongs\Service\ProviderRegistry;
use Loongs\Support\BasePath;
use Loongs\Uploads\Uploads;

/** @var \Loongs\Routing\Router $router */
/** @var \Loongs\Http\Application $app */
/** @var \Loongs\Container\Container $container */

$config = $container->make(Repository::class);

// loongs/uploads
Uploads::configure((array) $config->get('uploads', []));

// Languages (loongs/language): packs apps/*/lang/<locale>/*.php|json
$container->singleton(Translator::class, static fn (): Translator => I18n::boot(BasePath::get('apps')));
$container->make(Translator::class);

// Menus main service: ProviderRegistry + MenusService aggregator
$container->singleton(ProviderRegistry::class, static fn (): ProviderRegistry => new ProviderRegistry());
$container->singleton(MenusService::class, static function () use ($container): MenusService {
    return new MenusService(
        $container->make(ProviderRegistry::class),
        $container->make(RpcClientInterface::class),
        BasePath::get('apps'),
        BasePath::get('apps/Admin'),
    );
});
$container->singleton(MenuRegistry::class, static function () use ($container): MenuRegistry {
    return $container->make(MenusService::class)->buildRegistry();
});

$container->singleton(PermissionService::class, static fn (): PermissionService => new PermissionService(
    $container->make(CacheManager::class),
    $container->make(MenuRegistry::class),
    (int) $config->get('admin.permission_cache_ttl', $config->get('uuims.permission_cache_ttl', 600)),
));

// Server-rendered pages (loongs/render)
$container->singleton(PageRegistry::class, static function () use ($container): PageRegistry {
    $registry = new PageRegistry(
        static fn (string $class): object => $container->make($class),
        translator: new RenderTranslator($container->make(Translator::class)),
    );
    $registry->discoverApps(BasePath::get('apps'));

    return $registry;
});
$container->singleton(PageService::class, static fn (): PageService => new PageService(
    $container->make(PageRegistry::class),
    $container->make(MenuRegistry::class),
    $container->make(PermissionService::class),
    $container->make(CacheManager::class),
    (int) $config->get('admin.page_cache_ttl', $config->get('uuims.page_cache_ttl', 600)),
));

$files = glob(__DIR__ . '/routes/*.php') ?: [];
sort($files, SORT_STRING);
foreach ($files as $file) {
    require $file;
}
