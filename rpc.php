<?php

declare(strict_types=1);

/**
 * Admin RPC registration (AppDiscovery hook).
 *
 * Auto service key: `admin` (local) via apps/Admin/rpc.php.
 *
 * Menus main service lives HERE (moved from Uuims):
 *   Menus.register / Menus.unregister / Menus.providers / Menus.export
 *
 * Auth stays on Uuims: call $rpc->call('uuims', 'Auth.check'|'Auth.can', …).
 * Callers that previously targeted uuims Menus.* must retarget service `admin`.
 *
 * @var \Loongs\Rpc\Server\HandlerRegistry $handlers
 * @var \Loongs\Container\Container $container
 */

use App\Admin\Services\MenusService;
use Loongs\Rpc\Contract\RpcRequest;

$handlers->register('admin', 'ping', static function (RpcRequest $request): array {
    return [
        'ok' => true,
        'app' => 'Admin',
        'echo' => $request->payload,
        'service' => $request->service,
        'method' => $request->method,
    ];
});

// --- Menus main service (logical id = menus) ---
$handlers->register('admin', 'Menus.register', static function (RpcRequest $request) use ($container): array {
    /** @var MenusService $svc */
    $svc = $container->make(MenusService::class);
    $payload = is_array($request->payload) ? $request->payload : [];

    return $svc->register($payload);
});

$handlers->register('admin', 'Menus.unregister', static function (RpcRequest $request) use ($container): array {
    /** @var MenusService $svc */
    $svc = $container->make(MenusService::class);
    $payload = is_array($request->payload) ? $request->payload : [];

    return $svc->unregister($payload);
});

$handlers->register('admin', 'Menus.providers', static function (RpcRequest $request) use ($container): array {
    /** @var MenusService $svc */
    $svc = $container->make(MenusService::class);

    return $svc->listProviders();
});

$handlers->register('admin', 'Menus.export', static function (RpcRequest $request) use ($container): array {
    /** @var MenusService $svc */
    $svc = $container->make(MenusService::class);

    return $svc->exportOwn();
});
