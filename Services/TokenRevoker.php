<?php

declare(strict_types=1);

namespace App\Admin\Services;

use Loongs\Orm\Orm;

/**
 * Force-logout helper used by Admin user CRUD (password reset / disable / delete).
 *
 * Writes oauth_* tables directly for now (same physical store as Uuims auth).
 * Prefer a future Uuims RPC (e.g. Auth.revokeAll) and delete this class.
 */
final class TokenRevoker
{
    public static function revokeAll(int $userId): int
    {
        $now = time();
        $n = Orm::table('oauth_access_tokens')->where('user_id', (string) $userId)->whereNull('revoked_at')->update(['revoked_at' => $now]);
        $n += Orm::table('oauth_refresh_tokens')->where('user_id', (string) $userId)->whereNull('revoked_at')->update(['revoked_at' => $now]);

        return $n;
    }
}
