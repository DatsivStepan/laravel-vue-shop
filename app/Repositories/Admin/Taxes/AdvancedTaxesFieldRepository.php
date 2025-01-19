<?php

namespace App\Repositories\Admin\Taxes;

use App\Models\Taxes\AdvancedTaxesField;
use App\Models\User;
use Spatie\QueryBuilder\QueryBuilder;

class AdvancedTaxesFieldRepository
{
    /**
     * @param int|null $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate(int $perPage = null)
    {
        return QueryBuilder::for(AdvancedTaxesField::class)
            ->allowedFilters([
                'name',
            ])
            ->allowedSorts(['name', 'created_at'])
            ->paginate($perPage);
    }

    /**
     * @param array $payload
     * @return AdvancedTaxesField
     */
    public function create(array $payload)
    {
        $payload['model_class'] = User::class;

        return AdvancedTaxesField::create($payload);
    }

    /**
     * @param $ids
     * @return bool|null
     */
    public function delete($ids)
    {
        $ids = is_array($ids) ? $ids : func_get_args();

        return AdvancedTaxesField::destroy($ids);
    }

    /**
     * @param $id
     * @return AdvancedTaxesField|null
     */
    public function find($id)
    {
        return AdvancedTaxesField::find($id);
    }
}
