<?php

namespace App\Repositories\Admin;

use App\Models\Roles;
use App\Repositories\BaseRepository;

class RolesRepository extends BaseRepository
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return Roles::class;
    }
}
