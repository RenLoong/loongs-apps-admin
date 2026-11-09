<?php

declare(strict_types=1);

use Loongs\Http\Request;
use Loongs\Http\Response;

/** @var \Loongs\Routing\Router $router */

$router->get('/admin/api/ping', static function (Request $request): Response {
    return (new Response())->json(data: ['pong' => true, 'app' => 'Admin', 'time' => date('c')]);
});
