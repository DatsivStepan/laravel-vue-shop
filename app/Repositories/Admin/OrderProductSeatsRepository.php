<?php

namespace App\Repositories\Admin;

use App\Models\OrdersProductSeats;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class OrderProductSeatsRepository extends BaseRepository
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return OrdersProductSeats::class;
    }

    /**
     * @param $orderProducts
     */
    public function createdSeats($orderProducts)
    {
        if (!$orderProducts->count()) {
            return false;
        }

        DB::transaction(function () use ($orderProducts) {
            foreach ($orderProducts->get() as $orderProduct) {
                if ($orderProduct->product && $orderProduct->product->enable_seats) {
                    $order = $orderProduct->order;
                    $productId = $orderProduct->product_id;
                    $useSeat = $order->productSeatsUsed()
                        ->whereHas('orderProduct',
                            function (Builder $query) use ($productId) {
                                $query->where('product_id', $productId);
                            })->exists();
                    if (!$useSeat) {
                        $this->create([
                            'order_product_id' => $orderProduct->id,
                            'seat_key' => $this->generateKey()
                        ]);
                    }
                }
            }

            return true;
        });

        return false;
    }

    /**
     * @return string
     */
    public function generateKey()
    {
        $key = "";
        $codealphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codealphabet .= "0123456789";
        $max = strlen($codealphabet);

        for ($i = 0; $i < 16; $i++) {
            if (in_array($i, array(4, 8, 12))) {
                $key .= '-';
            }
            $key .= $codealphabet[random_int(0, $max - 1)];
        }

        if ($this->getOne($key, 'seat_key')) {
            $key = $this->generateKey();

        }

        return $key;
    }
}
