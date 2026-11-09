<?php

declare(strict_types=1);

use App\Admin\Middleware\AdminAuthMiddleware;

/** Profile / menus for the Admin SPA; bearer via Uuims RPC Auth.check. */
return [
    AdminAuthMiddleware::class,
];