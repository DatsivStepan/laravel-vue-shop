<?php

namespace App\Repositories\Admin;

use App\Models\ProductCourses;
use App\Repositories\BaseRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;

class ProductCoursesRepository extends BaseRepository
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return ProductCourses::class;
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
            $modelClass = $this->getModelClass();
            $model = new $modelClass();
            $model->fill($data);

            $courses = $data['course_id'];
            if (is_array($courses)) {
                foreach ($courses as $course) {
                    $this->updateOrCreate([
                        'course_id' => $course['value'],
                        'product_id' => $model->product_id
                    ]);
                }
            }

            DB::commit();
            return $model;
        } catch (Exception $e) {
            DB::rollback();
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
     * @return AllowedFilter
     */
    public function getWithQuery($perPage = null)
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
