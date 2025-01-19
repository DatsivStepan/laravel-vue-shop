<?php

namespace App\Repositories\Admin;

use App\Models\Currency;
use App\Repositories\BaseRepository;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CurrencyRepository extends BaseRepository
{
    /**
     * @return Currency
     */
    protected function getModelClass(): string
    {
        return Currency::class;
    }

    /**
     * @param int $perPage
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getCurrencyWithPagination(int $perPage)
    {
        return QueryBuilder::for(Currency::class)
            ->allowedFilters([
                AllowedFilter::exact('active'),
                AllowedFilter::scope('search'),
                'name'
            ])
            ->allowedSorts(['name', 'code', 'symbol'])
            ->paginate($perPage);
    }

    public function getCurrencyForSelect()
    {
        return QueryBuilder::for(Currency::class)
            ->allowedFilters([
                AllowedFilter::exact('active')
            ])
            ->get();
    }

    public function findByCode($code, $with = [])
    {
        return $this->getModelClass()::query()->with($with)->where('code', $code)->firstOrFail();
    }
}
