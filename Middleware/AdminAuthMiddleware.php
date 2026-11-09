<?php

declare(strict_types=1);

namespace App\Admin\Middleware;

use App\Admin\Models\Uuims;
use App\Admin\Services\PermissionService;
use App\Admin\Support\AdminContext;
use App\Admin\Support\Api;
use Closure;
use Loongs\Http\Request;
use Loongs\Http\Response;
use Loongs\Middleware\MiddlewareInterface;
use Loongs\Rpc\Client\RpcClientInterface;

/**
 * Authenticate Admin HTTP via Uuims RPC Auth.check (bearer), then build the full
 * permission context locally against uuims_* identity tables (data scope / fields).
 *
 * Does NOT host OAuth ResourceServer here — tokens stay owned by Uuims.
 */
final class AdminAuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly RpcClientInterface $rpc,
        private readonly PermissionService $permissions,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $authorization = (string) ($request->header('Authorization') ?? '');
        $rpcResponse = $this->rpc->call('uuims', 'Auth.check', [
            'authorization' => $authorization,
        ]);

        if (!$rpcResponse->isOk()) {
            $code = (int) ($rpcResponse->code ?: 40101);
            $http = match ($code) {
                40300 => 403,
                40001 => 400,
                default => 401,
            };
            $message = $rpcResponse->message !== '' ? $rpcResponse->message : '未登录或登录已失效';

            $data = is_array($rpcResponse->data) ? $rpcResponse->data : null;

            return Api::fail($code, $message, $http, $data);
        }

        /** @var array<string, mixed> $proj */
        $proj = is_array($rpcResponse->data) ? $rpcResponse->data : [];
        $userId = (int) ($proj['id'] ?? 0);
        $uuims = $userId > 0
            ? Uuims::query()->withoutGlobalScope('data_scope')->find($userId)
            : null;
        if ($uuims === null || (int) $uuims->status !== 1) {
            return Api::fail(40102, '账号不存在或已停用', 401);
        }

        // Full context (perms + data scope + field perms) from local uuims_* tables.
        $ctx = $this->permissions->context($uuims);
        AdminContext::set($ctx);
        $request->setAttribute('user_ctx', $ctx);
        $request->setAttribute('auth_rpc', $proj);

        return $next($request);
    }
}
