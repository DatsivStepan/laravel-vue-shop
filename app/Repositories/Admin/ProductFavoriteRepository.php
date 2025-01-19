<?php

namespace App\Repositories\Admin;

use App\Models\ProductFavorites;
use App\Repositories\BaseRepository;
use Spatie\QueryBuilder\AllowedFilter;

class ProductFavoriteRepository extends BaseRepository
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return ProductFavorites::class;
    }

    /**
     * @param null|int $perPage
     * @return AllowedFilter
     */
    public function getWithQuery($perPage = null)
    {
        $filters = [
            AllowedFilter::scope('search'),
            'user_id',
            'created_at',
        ];

        return $this->getWithQueryBuilder(
            $filters,
            [
                'created_at',
            ],
            $perPage
        );
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
}
