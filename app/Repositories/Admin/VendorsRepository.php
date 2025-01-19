<?php

namespace App\Repositories\Admin;

use App\Classes\Enum\ModelStatus;
use App\Models\Files;
use App\Models\Vendors;
use App\Repositories\BaseRepository;
use App\Services\Sort\DefaultSort;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class VendorsRepository extends BaseRepository
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return Vendors::class;
    }

    /**
     * @param int $instanceType
     * @param int $instanceId
     * @return array
     */
    public static function getVendorsInArray(int $instanceType = 0, int $instanceId = 0)
    {
        $options = [
            'whereDoesntHave' => [
                'table' => 'assigns',
                'query' => function ($query) use ($instanceType, $instanceId) {
                    $query->where('instance_id', $instanceId)
                        ->where('instance_type', $instanceType);
                }
            ]
        ];

        return Arr::pluck((new self)->get($options)->toArray(), 'name', 'id');
    }

    /**
     * @param array $data
     * @return Model
     * @throws Exception
     */
    public function create(array $data)
    {
        $model = parent::create($data);

        $this->afterSave($model, $data);

        return $model;
    }

    /**
     * @param Model $model
     * @param array $data
     * @return Model
     * @throws Exception
     */
    public function update($model, array $data)
    {
        $model = parent::update($model, $data);

        $this->afterSave($model, $data);

        return $model;
    }

    /**
     * @param Model $model
     * @param array $data
     */
    public function afterSave($model, array $data)
    {
        if (array_key_exists('image_src', $data)) {
            (new FilesRepository)
                ->saveImages($model->id, Files::INSTANCE_TYPE_VENDORS, 'image', $data['image_src']);
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

    // ACTIONS
    /**
     * @param array|int $ids
     * @return bool
     */
    public function hide($ids)
    {
        $ids = is_array($ids) ? $ids : [$ids];
        return $this->updateIn($ids, ['status' => ModelStatus::INACTIVE]);
    }

    /**
     * @param array|int $ids
     * @return bool
     */
    public function show($ids)
    {
        $ids = is_array($ids) ? $ids : [$ids];
        return $this->updateIn($ids, ['status' => ModelStatus::ACTIVE]);
    }
    // ACTIONS

    /**
     * This method used in coupon assign all vendors
     *
     * @return Collection
     */
    public function getAllVendorsIds(): Collection
    {
        return ($this->getModelClass())::query()->pluck('id');
    }

    /**
     * @return array
     */
    protected function getSearchFields(): array
    {
        return $this->getModelClass()::getSearchFields();
    }

    /**
     * @param null|int $perPage
     * @return AllowedFilter
     */
    public function getWithQuery($perPage = null)
    {
        $filters = [
            AllowedFilter::scope('search'),
            $this->allowedFilterStatus(null, 'status'),
            'created_at',
        ];

        return $this->getWithQueryBuilder(
            $filters,
            ['name', 'email', 'company', 'created_at'],
            $perPage,
            AllowedSort::custom('created_at', new DefaultSort())->defaultDirection('desc')
        );
    }
}
