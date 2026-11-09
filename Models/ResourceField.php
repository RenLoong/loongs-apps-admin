<?php

declare(strict_types=1);

namespace App\Admin\Models;

use Loongs\Orm\Model\Model;

/**
 * TABLE OWNERSHIP: logical table `uuims_resource_fields` stays under Uuims identity schema
 * (physical lgs_uuims_resource_fields). Admin CRUD writes these tables for now; auth/token issuance stays in Uuims.
 * Future: prefer Uuims identity RPC and drop direct writes from Admin.
 */
final class ResourceField extends Model
{
    public bool $timestamps = false;
    protected string $table = 'uuims_resource_fields';
    protected array $fillable = ['resource', 'field', 'label', 'default_perm'];
    protected array $casts = ['id' => 'int'];
}
