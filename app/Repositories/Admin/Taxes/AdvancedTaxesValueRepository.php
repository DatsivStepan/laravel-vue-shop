<?php

namespace App\Repositories\Admin\Taxes;

use App\Models\Taxes\AdvancedTaxesValue;
use Spatie\QueryBuilder\QueryBuilder;

class AdvancedTaxesValueRepository
{
    /**
     * @param int $fieldId
     * @param int|null $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate(int $fieldId, int $perPage = null)
    {
        return QueryBuilder::for(AdvancedTaxesValue::class)
            ->where('field_id', $fieldId)
            ->allowedFilters([
                'value',
            ])
            ->allowedSorts(['value'])
            ->paginate($perPage);
    }

    /**
     * @param array $payload
     * @return AdvancedTaxesValue
     */
    public function create(array $payload)
    {
        return AdvancedTaxesValue::create($payload);
    }

    /**
     * @param $ids
     * @return bool|null
     */
    public function delete($ids)
    {
        $ids = is_array($ids) ? $ids : func_get_args();

        return AdvancedTaxesValue::destroy($ids);
    }

}
