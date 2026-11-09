<?php

declare(strict_types=1);

use App\Admin\Modules\Auth\Controller\ProfileController;

/** @var \Loongs\Routing\Router $router */

// Module middleware (AdminAuthMiddleware) applies. Profile for Admin SPA.
$router->get('/admin/api/auth/me', [ProfileController::class, 'me']);
$router->get('/admin/api/auth/menus', [ProfileController::class, 'menus']);
$router->put('/admin/api/auth/password', [ProfileController::class, 'password']);