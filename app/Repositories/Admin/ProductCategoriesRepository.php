<?php

namespace App\Repositories\Admin;

use App\Models\ProductCategories;
use App\Repositories\BaseRepository;

class ProductCategoriesRepository extends BaseRepository
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return ProductCategories::class;
    }
}
