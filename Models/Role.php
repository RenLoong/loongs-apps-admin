<?php

declare(strict_types=1);

namespace App\Admin\Models;

use Loongs\Orm\Model\Model;

/**
 * TABLE OWNERSHIP: logical table `uuims_roles` stays under Uuims identity schema
 * (physical lgs_uuims_roles). Admin CRUD writes these tables for now; auth/token issuance stays in Uuims.
 * Future: prefer Uuims identity RPC and drop direct writes from Admin.
 */
final class Role extends Model
{
    protected string $table = 'uuims_roles';
    protected array $fillable = ['code', 'name', 'sort', 'status', 'remark', 'created_by'];
    protected array $casts = ['id' => 'int', 'sort' => 'int', 'status' => 'int'];
}
