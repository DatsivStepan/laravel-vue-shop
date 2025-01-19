<?php

namespace App\Services;

use App\Models\Products;
use App\Models\ProductWaitlist;
use App\Notifications\ProductAvailableNotification;

class ProductWaitListService
{
    /**
     * @param ProductWaitlist $productWaitList
     *
     * @return void
     */
    public function sendEmailToUser(ProductWaitlist $productWaitList)
    {
        if (!$productWaitList->sent) {
            $productWaitList->user->notify(new ProductAvailableNotification($productWaitList));
            $productWaitList->update([
                'sent' => ProductWaitlist::STATUS_SENT,
            ]);
        }
    }

    /**
     *
     * @return void
     */
    public function deleteInactive()
    {
        ProductWaitlist::query()->inActive()->delete();
    }

    /**
     * @return int
     */
    public function checkingAndSendingNotificationUsersAboutAvailableProducts(): int
    {
        $sumSeats = 0;
        Products::query()->has('waitlist')
            ->each(function (Products $product) use (&$sumSeats) {
                $aSeats = $product->getAvailableSeatsWithoutWaitList() - ProductWaitlist::query()->active()
                        ->where('product_id', $product->id)
                        ->where('sent', 1)->count();
                $sumSeats += $aSeats;

                if ($aSeats > 0) {
                    $product->waitlist()
                        ->noSent()
                        ->take($aSeats)
                        ->orderBy('created_at')
                        ->get()
                        ->each(function (ProductWaitlist $productWaitList) {
                            $this->sendEmailToUser($productWaitList);
                        });
                }
            });

        return $sumSeats;
    }
}
