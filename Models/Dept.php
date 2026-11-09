<?php

declare(strict_types=1);

namespace App\Admin\Models;

use Loongs\Orm\Model\Model;

/**
 * TABLE OWNERSHIP: logical table `uuims_depts` stays under Uuims identity schema
 * (physical lgs_uuims_depts). Admin CRUD writes these tables for now; auth/token issuance stays in Uuims.
 * Future: prefer Uuims identity RPC and drop direct writes from Admin.
 */
final class Dept extends Model
{
    protected string $table = 'uuims_depts';
    protected array $fillable = ['parent_id', 'ancestors', 'name', 'sort', 'leader', 'phone', 'status', 'created_by'];
    protected array $casts = ['id' => 'int', 'parent_id' => 'int', 'sort' => 'int', 'status' => 'int'];
}
