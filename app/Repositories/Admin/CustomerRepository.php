<?php

namespace App\Repositories\Admin;

use App\Models\UserData;
use App\Repositories\BaseRepository;

class CustomerRepository extends BaseRepository
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return UserData::class;
    }
}
