<?php

namespace App\Repositories;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

abstract class BaseRepository
{
    /**
     * @param array $options
     * @return Collection
     */
    public function get(array $options = []): Collection
    {
        /** @var Builder $query */
        $query = ($this->getModelClass())::query();
        $this->applyOptions($query, $options);

        return $query->get();
    }

    /**
     * @param array $options
     * @return Model
     */
    public function getOneWithOptions(array $options = [])
    {
        /** @var Builder $query */
        $query = ($this->getModelClass())::query();
        $this->applyOptions($query, $options);

        return $query->first();
    }

    /**
     * @param int $value
     * @param array $options
     * @return Model
     */
    public function paginate($value = 10, $options = []): LengthAwarePaginator
    {
        $query = ($this->getModelClass())::query();
        $this->applyOptions($query, $options);

        return $query->paginate($value);
    }

    public function count(array $options = []): Collection
    {
        /** @var Builder $query */
        $query = ($this->getModelClass())::query();
        $this->applyOptions($query, $options);

        return $query->get();
    }

    /**
     * @param int $value
     * @param string $attribute
     * @return Model
     */
    public function getOne($value, $attribute = 'id')
    {
        return ($this->getModelClass())::where($attribute, '=', $value)
            ->first();
    }

    /**
     * @param array $conditions
     * @param array $data
     * @return Model
     */
    public function updateOrCreate(array $conditions, array $data = [])
    {
        return ($this->getModelClass())::updateOrCreate($conditions, $data);
    }

    /**
     * @param int $value
     * @param string $attribute
     * @return Model
     */
    public function getOneOrFail($value, $attribute = 'id')
    {
        return ($this->getModelClass())::where($attribute, '=', $value)
            ->firstOrFail();
    }

    /**
     * @param array $conditions
     * @param array $with
     *
     * @return Builder
     */
    public function firstOrCreate(array $conditions, $with = [])
    {
        return ($this->getModelClass())::query()->with($with)->firstOrCreate($conditions);
    }

    /**
     * @param array $conditions
     * @param null|string $with
     * @return Model
     * @throws Exception
     */
    public function getOneByConditions(array $conditions, $with = [])
    {
        $query = ($this->getModelClass())::query();

        foreach ($conditions as $key => $value) {
            $query->where(DB::raw($key), $value);
        }

        if (!empty($with)) {
            $query->with($with);
        }

        return $query->first();
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
            return $model;
        }

        throw new Exception('Cannot create model ' . $this->getModelClass());
    }

    /**
     * @param Model $model
     * @param array $data
     * @return Model
     * @throws Exception
     */
    public function update(Model $model, array $data)
    {
        $model->fill($data);

        if ($model->save()) {
            return $model;
        }
        throw new Exception('Cannot update model ' . $this->getModelClass());
    }

    /**
     * @param array $conditions List of conditions
     * @param array $data
     * @return bool
     */
    public function updateByConditions(array $conditions, array $data)
    {
        /** @var Model $instance */
        $instance = ($this->getModelClass())::where($conditions)->first();

        if ($instance) {
            $instance->fill($data);
            return $instance->save();
        }

        return false;
    }

    /**
     * @param Model $model
     * @return Model
     * @throws Exception
     */
    public function delete(Model $model)
    {
        if ($model->delete()) {
            return $model;
        }
        throw new Exception('Cannot delete model ' . $this->getModelClass());
    }

    /**
     * Needed for mass delete.
     *
     * @param array $ids
     *
     * @return mixed
     */
    public function deleteIn(array $ids)
    {
        return $this->getModelClass()::query()->whereIn('id', $ids)
            ->delete();
    }

    /**
     * Needed for mass update of parameters. For example with bulk actions.
     *
     * @param array $ids
     *
     * @param array $data
     *
     * @return int
     */
    public function updateIn(array $ids, array $data)
    {
        return $this->getModelClass()::query()->whereIn('id', $ids)
            ->update($data);
    }

    /**
     * @return Model
     */
    protected abstract function getModelClass(): string;

    protected function applyOptions(Builder $query, array $options): void
    {
        if (!empty($options['with'])) {
            $query->with($options['with']);
        }

        if (!empty($options['where'])) {
            foreach ($options['where'] as $where) {
                $query->where($where['column'], $where['operator'], $where['value']);
            }
        }

        if (!empty($options['doesntHave'])) {
            $query->doesntHave($options['doesntHave']);
        }

        if (!empty($options['between'])) {
            $query->whereBetween($options['between']['field'], $options['between']['values']);
        }

        if (!empty($options['whereHas'])) {

            if (isset($options['whereHas'][0]) && is_array($options['whereHas'][0])) {
                foreach ($options['whereHas'] as $whereHas) {
                    $query->whereHas($whereHas['table'], $whereHas['query']);
                }
            } else {
                $query->whereHas($options['whereHas']['table'], $options['whereHas']['query']);
            }
        }

        if (!empty($options['whereDoesntHave'])) {
            $query->whereDoesntHave($options['whereDoesntHave']['table'], $options['whereDoesntHave']['query']);
        }

        if (!empty($options['orWhereHas'])) {
            $query->orWhereHas($options['orWhereHas']['table'], $options['orWhereHas']['query']);
        }

        if (!empty($options['sort']['field'])) {
            $query->orderBy($options['sort']['field'], $options['sort']['direction'] ?? 'asc');
        }

        if (!empty($options['limit'])) {
            $query->limit($options['limit']);
        }

        if (!empty($options['offset'])) {
            $query->offset($options['offset']);
        }

        $this->applyFilters($query, $options);
    }

    protected function applyFilters(Builder $query, array $options): void
    {
        if ($this->getSearchFields()) {
            if (!empty($options['search'])) {
                $query->where(function (Builder $query) use ($options) {
                    foreach ($this->getSearchFields() as $field) {
                        $query->orWhere($field, 'like', '%' . $options['search'] . '%');
                    }
                });
            }
        }

        if (!empty($options['filters'])) {
            foreach ($options['filters'] as $key => $values) {
                $query->whereIn($key, is_array($values) ? $values : [$values]);
            }
        }

        if (!empty($options['notInFilters'])) {
            foreach ($options['notInFilters'] as $key => $values) {
                $query->whereNotIn($key, is_array($values) ? $values : [$values]);
            }
        }
    }

    protected function getSearchFields(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getDefaultSort(): array
    {
        return [];
    }

    protected function getRawSelect($sql, $params = []): array
    {
        $sql = str_replace('pfx_', \DB::getTablePrefix(), $sql);
        return \DB::select(\DB::raw($sql), $params);
    }

    protected function getRawOne($sql, $params = []): object
    {
        $sql = str_replace('pfx_', \DB::getTablePrefix(), $sql);
        return \DB::selectOne(\DB::raw($sql), $params);
    }

    /**
     * @param array $options
     * @return bool
     */
    public function deleteAllItems(array $options = [])
    {
        /** @var Builder $query */
        $query = ($this->getModelClass())::query();
        $this->applyOptions($query, $options);

        return $query->delete();
    }

    public function find($id)
    {
        return $this->getModelClass()::find($id);
    }

    /**
     * @param $id
     * @param null $with
     *
     * @return mixed
     */
    public function findOrFail($id, $with = [])
    {
        return $this->getModelClass()::query()->with($with)->findOrFail($id);
    }

    /**
     * @param array $items
     * @param string $table
     * @param string $sortField
     * @return array
     */
    public function reloadSortOrder($items, $table, $sortField = 'sortorder')
    {
        $options = ['filters' => ['id' => $items]];
        $models = $this->get($options);
        if (!$models) {
            throw new Exception('Not find sort models');
        }

        $arrayItems = Arr::pluck($models, $sortField);
        rsort($arrayItems);

        $cases = $params = $ids = [];
        foreach ($arrayItems as $key => $value) {
            $cases[] = "WHEN {$items[$key]} then ?";
            $params[] = $value;
            $ids[] = $items[$key];
        }

        $ids = implode(',', $ids);
        $cases = implode(' ', $cases);

        return \DB::update("UPDATE " . $table . " SET `" . $sortField . "` = CASE `id` {$cases} END WHERE `id` in ({$ids})",
            $params);
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        $model = $this->getModelClass();
        return (new $model)->getTable();
    }

    /**
     * @param null|string $table
     * @param string $field
     * @param null|User $user
     * @return AllowedFilter
     */
    public function allowedFilterStatus($table = null, $field = 'visible', $user = null)
    {
        if (!$table) {
            $table = $this->getTableName();
        }

        $status = AllowedFilter::exact($field);
        if ($user) {
            $filters = request()->get('filter');
            if ($filters && array_key_exists($field, $filters)) {
                $user->settings()->set($table . '_filter_status', $filters[$field]);
            }
            $status->default($user->settings()->get($table . '_filter_status'));
        }

        return $status;
    }

    /**
     * @param array|int $filters
     * @param array|int $sorts
     * @param null|int $perPage
     * @param null|Spatie\QueryBuilder\AllowedSort $defaultSort
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getWithQueryBuilder(array $filters, array $sorts, $perPage = null, $defaultSort = null)
    {
        $query = QueryBuilder::for($this->getModelClass())
            ->allowedFilters($filters)
            ->allowedSorts($sorts);

        if ($defaultSort) {
            $query->defaultSort($defaultSort);
        }

        return $perPage ? $query->paginate($perPage) : $query->get();
    }
}
