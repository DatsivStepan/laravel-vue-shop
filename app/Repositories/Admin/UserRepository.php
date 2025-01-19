<?php

namespace App\Repositories\Admin;

use App\Models\User;
use App\Repositories\BaseRepository;
use Spatie\QueryBuilder\AllowedFilter;

class UserRepository extends BaseRepository
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return User::class;
    }

    /**
     * @param null|User $user
     * @param null|int $perPage
     * @return AllowedFilter
     */
    public function getWithQuery($perPage = null)
    {
        $filters = [
            AllowedFilter::scope('search'),
            AllowedFilter::scope('by_vendors'),
            'created_at',
        ];

        $sorts = [
            'id', 'name', 'email','created_at',
        ];

        return $this->getWithQueryBuilder(
            $filters,
            $sorts,
            $perPage
        );
    }
}
