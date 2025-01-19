<?php

namespace App\Repositories\Admin;

use App\Models\Courses;
use App\Models\Connection;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Cache;

class CourseRepository extends BaseRepository
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return Courses::class;
    }

    /**
     * @param string $search
     * @return array
     */
    public function updateCourseApi($search = '')
    {
        $connectionId = getSetting('connection_id');
        if ($connection = Connection::where('connection_id', $connectionId)->first()) {
            $timeCourseReload = Cache::get('timeCourseReload' . $connection->connection_id);
            $timeCourseReload = false;
            if (!$timeCourseReload || ($timeCourseReload && ($timeCourseReload < time()))) {
                $connection->updateCoursesApi($search);
                $connection->updateCoursesCategoriesApi();

                Cache::put('timeCourseReload' . $connection->connection_id, strtotime('+1 hour'));
            }
        }

    }

    /**
     * @param bool $reload
     * @param array $notAssignProductId
     * @param int $categoryId
     * @param string $query
     * @return array
     */
    public function getCoursesForSelect(bool $reload = false, $categoryId = 0, array $notAssignProductIds = [], $query = '')
    {
        if ($reload) {
            $this->updateCourseApi($query);
        }

        $options = [];
        if ($notAssignProductIds) {
            $options['doesntHave'] = 'productsAssigns';
            $options['orWhereHas'] = [
                'table' => 'productsAssigns',
                'query' => function ($query) use ($notAssignProductIds) {
                    $query->whereNotIn('product_id', $notAssignProductIds);
                }
            ];
        }

        if ($categoryId) {
            $options['filters'] = [
                'category_id' => $categoryId
            ];
        }

        return (new self)->get($options);
    }
}
