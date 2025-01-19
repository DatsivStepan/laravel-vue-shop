<?php

namespace App\Repositories\Admin;

use App\Classes\Enum\ModelStatus;
use App\Events\Products\ProductCreated;
use App\Events\Products\ProductDeleted;
use App\Events\Products\ProductUpdated;
use App\Facades\UserSettings;
use App\Models\Files;
use App\Models\ProductCategories;
use App\Models\ProductInstructors;
use App\Models\Products;
use App\Models\User;
use App\Repositories\BaseRepository;
use App\Services\Sort\DefaultSort;
use App\Services\Sort\Products\CoursesSort;
use App\Services\Sort\Products\IdNumberSort;
use Arr;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class ProductsRepository extends BaseRepository
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return Products::class;
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
            $model = parent::create($this->prepare($data));
            $this->afterSave($model, $data, true);

            event(new ProductCreated($model));

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
            /** @var Products $model */
            $model = parent::update($model, $this->prepare($data));
            $this->afterSave($model, $data);

            event(new ProductUpdated($model));

            DB::commit();
            return $model;
        } catch (Exception $e) {
            DB::rollback();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param Model $model
     * @return Model
     * @throws Exception
     */
    public function delete($model)
    {
        $productId = $model->id;
        $model = parent::delete($model);

        event(new ProductDeleted($productId));

        return $model;
    }

    /**
     * @param Model $model
     * @param array $data
     */
    public function afterSave($model, array $data, $isNew = false)
    {
        if ($isNew) {
            $model->sortorder = $model->id;
            $model->save();
        }

        if (array_key_exists('image_src', $data)) {
            (new FilesRepository)
                ->saveImages($model->id, Files::INSTANCE_TYPE_PRODUCT, 'image', $data['image_src']);
        }

        if(array_key_exists('video_src', $data)) {
            (new FilesRepository)
                ->saveVideo($model->id, Files::INSTANCE_TYPE_PRODUCT, 'video', $data['video_src']);
        }

        if (array_key_exists('featured_image_src', $data)) {
            (new FilesRepository)
                ->saveImages($model->id, Files::INSTANCE_TYPE_PRODUCT, 'featuredImage', $data['featured_image_src']);
        }

        if (array_key_exists('customFields', $data)) {
            (new CustomFieldsValuesRepository)
                ->saveCustomFields($model->id, $data['customFields']);
        }

        if (array_key_exists('categories', $data) && ($categories = $data['categories'])) {
            $this->saveCategories($model->id, $categories);
        }

        if (array_key_exists('instructors', $data) && ($instructors = $data['instructors'])) {
            $this->saveInstructors($model->id, $instructors);
        }
    }

    /**
     * @param int $connectionId
     * @return array
     */
    public static function getMinMaxPrice(): array
    {
        return [
            'min' => Products::min('price'),
            'max' => Products::max('price')
        ];
    }

    /**
     * @param int $productId
     * @param array $categories
     * @return bool
     */
    public function saveCategories(int $productId, array $categories = [])
    {
        ProductCategories::where('product_id', $productId)->delete();

        if ($categories) {
            foreach ($categories as $categoryData) {
                if (is_array($categoryData) && isset($categoryData['value'])) {
                    ProductCategories::create([
                        'product_id' => $productId,
                        'category_id' => $categoryData['value'],
                    ]);
                }
            }
        }

        return true;
    }

    /**
     * @param int $productId
     * @return bool
     */
    public function featured(int $productId)
    {
        if ($product = $this->getOne($productId, 'id')) {
            $product->featured = $product->featured ? 0 : 1;
            return $product->save();
        }

        return false;
    }

    /**
     * @param int $productId
     * @param int $toId
     * @return bool
     */
    public function moveTo(int $productId, int $toId)
    {
        DB::beginTransaction();
        try {
            $productFrom = $this->getOne($productId, 'id');
            $productTo = $this->getOne($toId, 'id');
            if ($productFrom && $productTo) {
                $sorderFrom = $productFrom->sortorder;
                $productFrom->sortorder = $productTo->sortorder;
                $productTo->sortorder = $sorderFrom;
                if ($productFrom->save() && $productTo->save()) {
                    DB::commit();
                    return true;
                }
            }

            DB::rollback();
        } catch (Exception $e) {
            DB::rollback();
            throw new Exception($e->getMessage());
        }

        return false;
    }

    /**
     * @param int $productId
     * @param array $instrucrots
     * @return bool
     */
    public function saveInstructors(int $productId, array $instrucrots = [])
    {
        ProductInstructors::where('product_id', $productId)->delete();

        if ($instrucrots) {
            foreach ($instrucrots as $instructorData) {
                if (is_array($instructorData) && isset($instructorData['value'])) {
                    ProductInstructors::create([
                        'product_id' => $productId,
                        'instructor_id' => $instructorData['value'],
                    ]);
                }
            }
        }

        return true;
    }

    /**
     * @param [] $ids
     * @return bool
     */
    public function deleteAll($ids)
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
     * @param array $data
     * @return array
     */
    public function prepare($data): array
    {
        $data['time_start'] = (!empty($data['time_start'])) ? strtotime($data['time_start']) : 0;
        $data['time_end'] = (!empty($data['time_end'])) ? strtotime($data['time_end']) : 0;
        $data['enrol_start_date'] = (!empty($data['enrol_start_date'])) ? $data['enrol_start_date'] : null;

        return $data;
    }

    /**
     * This method used in coupon assign all products
     *
     * @return Collection
     */
    public function getAllProductIds(): Collection
    {
        return ($this->getModelClass())::query()->pluck('id');
    }

    /**
     * This method used when we need get products for select on assign
     *
     * @param array $productsIds
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getProductsWithout(array $productsIds)
    {
        return $this->getModelClass()::query()->select('id', 'name')->whereNotIn('id', $productsIds)->get();
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

    private function formatPriceFilter(User $user)
    {
        $userCurrency = UserSettings::getCurrency($user);
        $defaultCurrency = getSetting('currency');

        if ($userCurrency !== $defaultCurrency) {
            $filter = request('filter');
            if (isset($filter['price'])) {
                [$from, $to] = explode(',', $filter['price']);
                $formattedFrom = currency($from, $userCurrency, $defaultCurrency, false);
                $formattedTo = currency($to, $userCurrency, $defaultCurrency, false);

                $filter['price'] = join(',', [$formattedFrom, $formattedTo]);
            }
            request()->request->add(['filter' => $filter]);
        }
    }

    /**
     * @param null|User $user
     * @param null|int $perPage
     * @return \Spatie\QueryBuilder\AllowedFilter
     */
    public function getWithQuery($user = null, $perPage = null)
    {
        $customFieldsSearch = '';
        if ($filtersR = request()->get('filter')) {
            if (array_key_exists('custom_fields_search', $filtersR)) {
                $customFieldsSearch = $filtersR['custom_fields_search'];
                request()->request->add(['filter' => Arr::except($filtersR, ['custom_fields_search'])]);
            }
        }

        if ($user && getSetting('enable_vendors') && getSetting('enable_vendors_filter') && ($vIds = $user->getManagerVendorsIds())) {
            $requestData = request()->all();
            $filters = isset($requestData['filter']) ? $requestData['filter'] : [];
            $filters['vendors'] = $vIds;
            request()->request->add(['filter' => $filters]);
        }

        $this->formatPriceFilter($user);

        $filters = [
            AllowedFilter::scope('search'),
            AllowedFilter::scope('price'),
            AllowedFilter::scope('vendors'),
            AllowedFilter::scope('instructors'),
            AllowedFilter::scope('categories'),
            AllowedFilter::scope('my_products'),
            AllowedFilter::scope('my_favorites'),
            $this->allowedFilterStatus(null, 'visible', $user),
            'created_at',
        ];

        $filters[] = AllowedFilter::callback('custom_fields',
            function ($query, $customFields) use ($customFieldsSearch) {
            $customFields = explode(",", $customFields);
            $query->whereHas('customFieldsValues', function ($query) use ($customFieldsSearch, $customFields) {
                return $query->whereIn('field_id', $customFields)->where('value', 'like', "%$customFieldsSearch%");
            });
        });

        return $this->getWithQueryBuilder(
            $filters,
            [
                'name',
                AllowedSort::custom('id_number', new IdNumberSort())->defaultDirection('desc'),
                AllowedSort::custom('courses', new CoursesSort())->defaultDirection('desc'),
                'price',
                'created_at'
            ],
            $perPage,
            AllowedSort::custom('sortorder', new DefaultSort())->defaultDirection('desc')
        );
    }
}
