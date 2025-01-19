<?php

namespace App\Repositories\Admin;

use App\Models\ProductInstructors;
use App\Repositories\BaseRepository;

class ProductInstructorsRepository extends BaseRepository
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return ProductInstructors::class;
    }
}
