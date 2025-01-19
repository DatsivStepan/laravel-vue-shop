<?php

namespace App\Repositories\Admin;

use App\Events\Discount\Created;
use App\Events\Discount\Updated;
use App\Models\Discount;
use App\Repositories\BaseRepository;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class DiscountRepository extends BaseRepository
{
    protected function getModelClass(): string
    {
        return Discount::class;
    }

    public function create(array $data): Discount
    {
        /** @var Discount $discount */
        $discount = parent::create($this->prepare($data));

        // Trigger event.
        Created::dispatch($discount);

        return $discount;
    }

    /**
     * @param Model $discount
     * @param array $data
     *
     * @return Discount
     * @throws Exception
     */
    public function update(Model $discount, array $data): Discount
    {
        /** @var Discount $discount */
        $discount = parent::update($discount, $this->prepare($data));

        Updated::dispatch($discount);

        return $discount;
    }

    /**
     * @param User $user
     * @param int $perPage
     * @return AllowedFilter
     */
    public function getWithQuery($user = null, $perPage = null)
    {
        $filters = [
            AllowedFilter::scope('search'),
            $this->allowedFilterStatus(null, 'status', $user),
        ];

        return $this->getWithQueryBuilder(
            $filters,
            [
                'name', 'time_start', 'time_end', 'discount', 'applied',
                'created_at'
            ],
            $perPage
        );
    }

    /**
     * TODO Add implementation
     *
     * @param int $discountId
     *
     * @return string
     */
    public function applied(int $discountId)
    {
        return '-';
    }

    private function prepare(array $data): array
    {
        return $data;
    }
}
