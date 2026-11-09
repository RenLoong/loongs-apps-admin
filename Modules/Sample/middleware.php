<?php

declare(strict_types=1);

use App\Admin\Middleware\AdminAuthMiddleware;

/** Sample data needs a valid uuims token; the permission is declared per route. */
return [
    AdminAuthMiddleware::class,
];
