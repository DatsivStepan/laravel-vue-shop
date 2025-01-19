<?php

namespace App\Repositories\Admin;

use App\Models\VendorsAssings;
use App\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;

class VendorsAssignRepository extends BaseRepository
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return VendorsAssings::class;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function assign(array $data)
    {
        try {
            if ($ids = $data['ids']) {
                foreach ($ids as $id) {
                    VendorsAssings::updateOrCreate([
                        'instance_type' => $data['instanceType'],
                        'instance_id' => $data['instanceId'],
                        'vendor_id' => $id['value']
                    ], $data);
                }
            }

            return true;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
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
     * @return LengthAwarePaginator
     */
    public function getWithQuery($perPage = null): LengthAwarePaginator
    {
        $filters = [
            AllowedFilter::scope('search'),
            'product_id',
            'created_at',
        ];

        return $this->getWithQueryBuilder(
            $filters,
            ['product_id', 'created_at'],
            $perPage
        );
    }
}
