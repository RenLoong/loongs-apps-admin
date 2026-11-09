<?php

declare(strict_types=1);

use App\Admin\Middleware\PermissionMiddleware as P;
use App\Admin\Modules\System\Controller\UuimsController;
use App\Admin\Modules\System\Controller\DeptController;
use App\Admin\Modules\System\Controller\FieldController;
use App\Admin\Modules\System\Controller\MenuController;
use App\Admin\Modules\System\Controller\RoleController;

/** @var \Loongs\Routing\Router $router */

$api = '/admin/api/system';

// Users (data scope + field permissions apply). Identity tables remain uuims_*.
$router->get("$api/users", [UuimsController::class, 'index'], [P::need('system:uuims:list')]);
$router->post("$api/users", [UuimsController::class, 'store'], [P::need('system:uuims:create')]);
$router->get("$api/users/{id}", [UuimsController::class, 'show'], [P::need('system:uuims:list')]);
$router->put("$api/users/{id}", [UuimsController::class, 'update'], [P::need('system:uuims:update')]);
$router->delete("$api/users/{id}", [UuimsController::class, 'destroy'], [P::need('system:uuims:delete')]);
$router->put("$api/users/{id}/password", [UuimsController::class, 'resetPassword'], [P::need('system:uuims:reset-password')]);
$router->delete("$api/users/{id}/wechat", [UuimsController::class, 'unbindWechat'], [P::need('system:uuims:unbind-wechat')]);

// Roles
$router->get("$api/roles", [RoleController::class, 'index'], [P::need('system:role:list')]);
$router->get("$api/roles/options", [RoleController::class, 'options'], [P::need('system:role:list', 'system:uuims:create', 'system:uuims:update')]);
$router->post("$api/roles", [RoleController::class, 'store'], [P::need('system:role:create')]);
$router->get("$api/roles/{id}", [RoleController::class, 'show'], [P::need('system:role:list')]);
$router->put("$api/roles/{id}", [RoleController::class, 'update'], [P::need('system:role:update')]);
$router->delete("$api/roles/{id}", [RoleController::class, 'destroy'], [P::need('system:role:delete')]);

// Menus / buttons: read-only merged tree
$router->get("$api/menus", [MenuController::class, 'index'], [P::need('system:menu:list', 'system:role:list')]);
$router->get("$api/menus/sources", [MenuController::class, 'sources'], [P::need('system:menu:list')]);

// Departments
$router->get("$api/depts", [DeptController::class, 'index'], [P::need('system:dept:list', 'system:uuims:list', 'system:role:list')]);
$router->get("$api/depts/{id}", [DeptController::class, 'show'], [P::need('system:dept:list')]);
$router->post("$api/depts", [DeptController::class, 'store'], [P::need('system:dept:create')]);
$router->put("$api/depts/{id}", [DeptController::class, 'update'], [P::need('system:dept:update')]);
$router->delete("$api/depts/{id}", [DeptController::class, 'destroy'], [P::need('system:dept:delete')]);

// Controllable fields
$router->get("$api/fields", [FieldController::class, 'index'], [P::need('system:role:list')]);
