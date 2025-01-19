<?php

namespace App\Repositories\Admin;

use App\Models\CoursesCategories;
use App\Repositories\BaseRepository;

class CourseCategoriesRepository extends BaseRepository
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return CoursesCategories::class;
    }
}
