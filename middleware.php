<?php

declare(strict_types=1);

use App\Admin\Middleware\ApiExceptionMiddleware;
use Loongs\Language\Integration\Framework\LocaleMiddleware;

/**
 * App-level middleware for every Admin route (after global RequestId/CORS).
 * LocaleMiddleware first; ApiExceptionMiddleware renders ApiException as JSON.
 * Auth is module-level (AdminAuthMiddleware → Uuims RPC Auth.check).
 */
return [
    LocaleMiddleware::class,
    ApiExceptionMiddleware::class,
];
