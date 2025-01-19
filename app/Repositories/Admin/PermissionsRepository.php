<?php

namespace App\Repositories\Admin;

use App\Models\Permissions;
use App\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PermissionsRepository extends BaseRepository
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return Permission::class;
    }

    /**
     * @param int $roleId
     * @param int $permissionId
     * @param bool $value
     * @return bool
     */
    public function changePermissionStatus(int $roleId, int $permissionId, bool $value): bool
    {
        if (!$roleModel = Role::findById($roleId)) {
            return false;
        }

        if ($permission = $this->getOne($permissionId, 'id')) {
            if ($value) {
                $roleModel->givePermissionTo($permission);
                $permission->assignRole($roleModel);
            } else {
                $roleModel->revokePermissionTo($permission);
                $permission->removeRole($roleModel);
            }
            return true;
        }

        return false;
    }

    /**
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function getWithPagination(int $perPage)
    {
        return QueryBuilder::for(Permissions::class)
            ->allowedFilters([
                AllowedFilter::scope('search'),
                'created_at',
            ])
            ->allowedSorts(['created_at'])
            ->paginate($perPage);
    }
}
