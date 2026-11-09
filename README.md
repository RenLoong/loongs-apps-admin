# loongs/admin (`App\Admin`)

后台管理 app：系统管理 / 消息 / 仪表盘 / 样例 / Render 页面 / 菜单主服务 / SPA 入口。

从 `loongs-apps-uuims@043b26f` 迁出（见 [loongs-apps-uuims#1](https://github.com/RenLoong/loongs-apps-uuims/pull/1)）。

## Auth

- **不**托管 OAuth / 登录 / 发令牌。
- HTTP：`AdminAuthMiddleware` → `$rpc->call('uuims', 'Auth.check', …)`，再本地 `PermissionService` 组 data scope / 字段权限。
- 令牌撤销：临时 `TokenRevoker` 写 `oauth_*`；后续应改为 Uuims RPC。

## Identity tables

Model `$table` 仍为 `uuims_users` / `uuims_roles` / `uuims_depts` / …（物理 `lgs_uuims_*`）。Admin CRUD 暂直写；认证归属 Uuims。

## Menus main

RPC service key：`admin`

| Method | 说明 |
|--------|------|
| `Menus.register` | 子服务注册 |
| `Menus.unregister` | 注销 |
| `Menus.providers` | 提供者列表 |
| `Menus.export` | 导出本 app menus |

**调用方改写**：原 `$rpc->call('uuims', 'Menus.*', …)` → `$rpc->call('admin', 'Menus.*', …)`。  
服务端 `config/rpc.php` 需登记 `admin`（及原指向 uuims 的 menus 主服务配置）。

## HTTP 前缀

| 旧 (Uuims) | 新 (Admin) |
|------------|------------|
| `/uuims/api/system/*` | `/admin/api/system/*` |
| `/uuims/api/messages*` | `/admin/api/messages*` |
| `/uuims/api/dashboard/*` | `/admin/api/dashboard/*` |
| `/uuims/api/samples/*` | `/admin/api/samples/*` |
| `/uuims/api/pages/{page}` | `/admin/api/pages/{page}` |
| `/uuims/web` `/uuims/h5` | `/admin/web` `/admin/h5` |

权限码（`system:uuims:*` 等）未改，避免打乱已落库角色授权。

## Page ids

全部 `uuims.*` → `admin.*`（如 `admin.system.users`）。打包的 Flutter/H5 仍可能写死旧 id 与 `/uuims/api`，需在 Admin 下重打 SPA。

## CLI

`./loongs admin:menus` — 离线校验合并菜单 + 页面。
