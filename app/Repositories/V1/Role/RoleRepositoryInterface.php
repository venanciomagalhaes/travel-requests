<?php

namespace App\Repositories\V1\Role;

use App\Enums\V1\Role\RolesNamesEnum;
use App\Models\Role;

interface RoleRepositoryInterface
{
    public function findByName(RolesNamesEnum $name): Role;
}
