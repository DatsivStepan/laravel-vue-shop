<?php

namespace App\Repositories\Admin;

use App\Models\RolesLms;
use App\Repositories\BaseRepository;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class RolesLmsRepository extends BaseRepository
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return RolesLms::class;
    }

    /**
     * @param array $ids
     * @return bool
     */
    public function deleteAll(array $ids)
    {
        return ($this->getModelClass())::query()
            ->whereIn('id', $ids)->delete();
    }

    /**
     * @param null|int $perPage
     * @return AllowedFilter
     */
    public function getWithQuery($perPage = null)
    {
        $filters = [
            AllowedFilter::scope('search'),
            'role_id',
            'created_at',
        ];

        return $this->getWithQueryBuilder(
            $filters,
            ['role_id', 'lms_role_name', 'created_at'],
            $perPage
        );
    }
}
