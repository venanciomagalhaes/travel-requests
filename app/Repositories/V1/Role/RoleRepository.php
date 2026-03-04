<?php

namespace App\Repositories\V1\Role;

use App\Enums\V1\Role\RolesNamesEnum;
use App\Models\Role;

class RoleRepository implements RoleRepositoryInterface
{
    public function findByName(RolesNamesEnum $name): Role
    {
        return Role::query()->where('name', $name->value)->firstOrFail();
    }
}
