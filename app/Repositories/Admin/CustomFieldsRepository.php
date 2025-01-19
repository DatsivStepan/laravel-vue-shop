<?php

namespace App\Repositories\Admin;

use App\Classes\Enum\CustomFieldType;
use App\Classes\Enum\ModelStatus;
use App\Models\CustomFields;
use App\Repositories\BaseRepository;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;

class CustomFieldsRepository extends BaseRepository
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return CustomFields::class;
    }

    /**
     * @param array $data
     * @return Model
     * @throws Exception
     */
    public function create(array $data)
    {
        $modelClass = $this->getModelClass();
        $model = new $modelClass();
        $model->fill($data);

        if ($model->save()) {
            $model->sortorder = $model->id;
            $model->save();

            return $model;
        }

        throw new Exception('Cannot create model ' . $this->getModelClass());
    }

    /**
     * @param array $ids
     * @param int $status
     * @return bool
     */
    public function changeVisible(array $ids, int $status)
    {
        return ($this->getModelClass())::query()
            ->whereIn('id', $ids)
            ->update(['visible' => $status]);
    }

    /**
     * @param string $instanceType
     * @param null|int $instanceId
     * @return bool
     */
    public function getFieldsForForm(string $instanceType, ?int $instanceId = null)
    {
        $responce = [];
        $options = [];
        if (!empty($requestData['instanceType'])) {
            $options['filters'] = [
                'instance_type' => $requestData['instanceType']
            ];
        }

        $options['with'] = [
            'customFieldValue' => function ($query) use ($instanceId) {
                $query->where('instance_id', $instanceId);
            }
        ];

        if ($models = $this->getActive($options)) {
            foreach ($models as $model) {
                $cValue = $model->customFieldValue;
                $responce[] = [
                    'title' => $model->title,
                    'name' => 'customField_' . $model->id,
                    'type' => $model->field_type,
                    'value' => $cValue ? json_decode($cValue->value) : ($model->field_type == CustomFieldType::FIELD_TYPE_MULTISELECT ? [] : null),
                    'options' => $model->getOptions(),
                    'required' => $model->required,
                    'errors' => null,
                ];
            }
        }

        return $responce;
    }

    public function getActive(array $options = [])
    {
        /** @var Builder $query */
        $query = ($this->getModelClass())::query();
        $this->applyOptions($query, $options);

        return $query->where('visible', 1)->get();
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
     * @return array
     */
    public function getDefaultSort(): array
    {
        return $this->getModelClass()::getDefaultSort();
    }

    /**
     * @return array
     */
    protected function getSearchFields(): array
    {
        return $this->getModelClass()::getSearchFields();
    }

    // ACTIONS
    /**
     * @param array|int $ids
     * @return bool
     */
    public function hide($ids)
    {
        $ids = is_array($ids) ? $ids : [$ids];
        return $this->updateIn($ids, ['visible' => ModelStatus::INACTIVE]);
    }

    /**
     * @param array|int $ids
     * @return bool
     */
    public function show($ids)
    {
        $ids = is_array($ids) ? $ids : [$ids];
        return $this->updateIn($ids, ['visible' => ModelStatus::ACTIVE]);
    }
    // ACTIONS

    /**
     * @param null|int $perPage
     * @return QueryBuilder
     */
    public function getWithQuery($perPage = null)
    {
        $filters = [
            AllowedFilter::scope('search'),
            'instance_type',
            'field_type',
            'visible',
            'created_at',
        ];

        return $this->getWithQueryBuilder(
            $filters,
            ['title', 'field_type', 'created_at'],
            $perPage
        );
    }
}
