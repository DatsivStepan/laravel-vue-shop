<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class OrderProductRepository
{
    public const TABLE_NAME = 'order_product';

    /**
     * @param $orderId
     * @param $productId
     *
     * @return int
     */
    public static function getProductQuantity($orderId, $productId): int
    {
        return DB::table(self::TABLE_NAME)
            ->where('order_id', $orderId)
            ->where('product_id', $productId)
            ->sum('quantity');
    }
}
