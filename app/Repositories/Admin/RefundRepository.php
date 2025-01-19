<?php

namespace App\Repositories\Admin;

use App\Models\Refund;
use App\Repositories\BaseRepository;

class RefundRepository extends BaseRepository
{

    /**
     * @return Refund
     */
    protected function getModelClass(): string
    {
        return Refund::class;
    }
}
