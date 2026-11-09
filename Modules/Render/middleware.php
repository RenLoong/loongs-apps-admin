<?php

declare(strict_types=1);

use App\Admin\Middleware\AdminAuthMiddleware;

/** Page descriptions need a valid uuims token; the page permission is checked by PageService. */
return [
    AdminAuthMiddleware::class,
];
