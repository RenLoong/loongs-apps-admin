<?php

declare(strict_types=1);

namespace App\Admin\Modules\Auth\Controller;

use App\Admin\Models\Dept;
use App\Admin\Models\Uuims;
use App\Admin\Services\PermissionService;
use App\Admin\Services\TokenRevoker;
use App\Admin\Support\AdminContext;
use App\Admin\Support\Api;
use App\Admin\Support\ApiException;
use App\Admin\Support\I18n;
use App\Admin\Support\Validator;
use Loongs\Http\Request;
use Loongs\Http\Response;

/**
 * Current administrator profile + sidebar menus for the Admin SPA.
 * OAuth token issuance stays in Uuims; this only reads local identity tables
 * after AdminAuthMiddleware has validated the bearer via RPC Auth.check.
 */
final class ProfileController
{
    public function __construct(
        private readonly PermissionService $permissions,
    ) {
    }

    public function me(Request $request): Response
    {
        $ctx = AdminContext::require();
        $uuims = Uuims::query()->withoutGlobalScope('data_scope')->findOrFail((int) $ctx['id']);
        $dept = $uuims->dept_id ? Dept::query()->find((int) $uuims->dept_id) : null;

        return Api::ok([
            'id' => (int) $uuims->id,
            'username' => $uuims->username,
            'nickname' => $uuims->nickname,
            'mobile' => $uuims->mobile,
            'email' => $uuims->email,
            'dept' => $dept === null ? null : ['id' => (int) $dept->id, 'name' => $dept->name],
            'is_super' => $ctx['is_super'],
            'roles' => $ctx['roles'],
            'permissions' => $ctx['is_super'] ? ['*'] : $ctx['perms'],
            'data_scope' => $ctx['data'],
            'fields' => $ctx['is_super'] ? new \stdClass() : $ctx['fields'],
            'last_login_at' => $uuims->last_login_at,
        ]);
    }

    public function menus(Request $request): Response
    {
        return Api::ok(I18n::tree($this->permissions->menuTree(AdminContext::require())));
    }

    /** Change own password — every token of this user is revoked (log in again). */
    public function password(Request $request): Response
    {
        $in = Validator::check(
            Api::input($request),
            ['old_password' => 'required|string', 'new_password' => 'required|string|min:8|max:72'],
            ['old_password' => '原密码', 'new_password' => '新密码'],
        );
        $uuims = Uuims::query()->withoutGlobalScope('data_scope')->findOrFail((int) AdminContext::require()['id']);
        if (!password_verify($in['old_password'], (string) $uuims->getAttribute('password'))) {
            throw new ApiException(42203, '原密码错误', 422);
        }
        $uuims->forceFill([
            'password' => Uuims::hashPassword($in['new_password']),
            'password_changed_at' => date('Y-m-d H:i:s'),
        ])->save();
        $revoked = TokenRevoker::revokeAll((int) $uuims->id);

        return Api::ok(['revoked_tokens' => $revoked], '密码已修改，请重新登录');
    }
}