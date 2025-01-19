<?php

namespace App\Repositories\Admin\Products;

use App\Models\Products\Installment;

class InstallmentRepository
{
    /**
     * @param $productId
     * @param array $data
     * @return Installment
     */
    public function create($productId, array $data): Installment
    {
        $data['product_id'] = $productId;

        return Installment::query()->create($data);
    }
}
