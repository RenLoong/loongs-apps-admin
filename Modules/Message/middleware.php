<?php

declare(strict_types=1);

use App\Admin\Middleware\AdminAuthMiddleware;

/** Messages need a valid uuims token; the inbox is per uuims (no permission code), sending needs message:send. */
return [
    AdminAuthMiddleware::class,
];
