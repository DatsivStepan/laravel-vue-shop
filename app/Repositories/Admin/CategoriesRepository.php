<?php

namespace App\Repositories\Admin;

use App\Classes\Enum\ModelStatus;
use App\Models\Categories;
use App\Models\Files;
use App\Repositories\BaseRepository;
use App\Services\Sort\DefaultSort;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class CategoriesRepository extends BaseRepository
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return Categories::class;
    }

    /**
     * @return array
     */
    public static function getCategoriesInArray()
    {
        $categoriesArray = Arr::pluck((new self)->get()->toArray(), 'name', 'id');
        return Arr::prepend($categoriesArray, __('categories.topcategory'), '0');
    }

    /**
     * @param array $data
     * @return Model
     * @throws Exception
     */
    public function create(array $data)
    {
        DB::beginTransaction();
        try {
            $model = parent::create($data);
            $this->afterSave($model, $data, true);

            DB::commit();
            return $model;
        } catch (Exception $e) {
            DB::rollback();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param Model $model
     * @param array $data
     * @return Model
     * @throws Exception
     */
    public function update($model, array $data)
    {
        DB::beginTransaction();
        try {
            $model = parent::update($model, $data);
            $this->afterSave($model, $data);

            DB::commit();
            return $model;
        } catch (Exception $e) {
            DB::rollback();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param Model $model
     * @param array $data
     * @param bool $isNew
     */
    public function afterSave($model, array $data, $isNew = false)
    {
        if ($isNew) {
            $model->sortorder = $model->id;
            $model->save();
        }

        if (array_key_exists('image_src', $data)) {
            (new FilesRepository)
                ->saveImages($model->id, Files::INSTANCE_TYPE_CATEGORY, 'image', $data['image_src']);
        }
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
     * @return array
     */
    public function getParent($id, &$categories = [])
    {
        $category = $this->getOne($id);

        $categories[] = [
            'id' => $category->id,
            'name' => $category->name,
        ];

        if ($category->parent_id) {
            return $this->getParent($category->parent_id, $categories);
        }

        return $categories;
    }

    /**
     * @return array
     */
    public function getParentCategories($id)
    {
        $category = $this->getOne($id);

        if ($category->parent_id) {
            return array_reverse($this->getParent($category->parent_id));
        }

        return [];
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

    /**
     * @param Model $category
     * @param array $categories
     * @return
     */
    public function getChildForTree($category)
    {
        $categoryArray = [
            'name' => $category->name,
            'path' => [
                'name' => 'catalog',
                'params' => ['id' => $category->id]
            ],
            'show' => true
        ];

        $childs = $category->childs;
        if ($childs->count()) {
            $cCategories = [];
            foreach ($childs as $child) {
                $cCategories[] = $this->getChildForTree($child);
            }
            $categoryArray['child'] = $cCategories;
        }

        return $categoryArray;
    }


    /**
     * @return
     */
    public function getTree()
    {
        $options = [
            'filters' => [
                'parent_id' => 0
            ]
        ];

        $res = [];
        if ($categories = $this->get($options)) {
            foreach ($categories as $category) {
                $res[] = $this->getChildForTree($category);
            }
        }

        return $res;
    }

    /**
     * @param null|User $user
     * @param null|int $perPage
     * @return AllowedFilter
     */
    public function getWithQuery($user = null, $perPage = null)
    {
        $filters = [
            AllowedFilter::scope('search'),
            $this->allowedFilterStatus(null, 'visible', $user),
            AllowedFilter::exact('parent_id')->default(0),
            'created_at',
        ];

        return $this->getWithQueryBuilder(
            $filters,
            ['id_number', 'name', 'created_at'],
            $perPage,
            AllowedSort::custom('sortorder', new DefaultSort())->defaultDirection('desc')
        );
    }
}
