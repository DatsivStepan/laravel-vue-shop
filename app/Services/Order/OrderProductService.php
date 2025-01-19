<?php

namespace App\Services\Order;

use App\Classes\Enum\Order\BillingType;
use App\Models\Coupons;
use App\Models\Discount;
use App\Models\Orders;
use App\Models\OrdersProductSeats;
use App\Models\Products;
use App\Models\User;
use App\Services\Discount\TypeRule\DiscountTypeRuleFactory;
use App\Services\Taxes\Tax;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class OrderProductService
{
    /**
     * @var Tax
     */
    private $tax;

    /**
     * OrderProductService constructor.
     * @param Tax $tax
     */
    public function __construct(Tax $tax)
    {
        $this->tax = $tax;
    }

    /**
     * @param Products $product
     * @param Orders $order
     * @return Products
     */
    private function calculateProductDiscount(Products $product, Orders $order): Products
    {
        $discount = 0;

        if (getSetting('enable_discounts') && $order->billing_type === BillingType::REGULAR) {
            $discountPercent = 0;

            $product->activeDiscounts()->each(
                function (Discount $discount) use (&$discountPercent, $order) {
                    $discountRule = DiscountTypeRuleFactory::make($discount->type);

                    if ($discountRule->passes($discount, $order)) {
                        $discountPercent += $discount->discount;
                    }
                }
            );

            $discount = $product->price * $discountPercent / 100;
        }

        $product->discount = $discount;
        $product->discount_price = $product->price - $discount;

        return $product;
    }

    /**
     * @param mixed $products
     * @param User $user
     * @return Collection
     */
    public function calculateProductsTax(&$products, User $user): Collection
    {
        $products = is_iterable($products) ? $products : collect()->push($products);

        $taxPercent = $this->tax->calculate($user);
        /** @var Products $product */
        foreach ($products as $product) {
            $product->tax = $this->calculateProductTax($product, $taxPercent);
        }

        return $products;
    }

    public function calculateProductTax(Products $product, $taxPercent)
    {
        if ($taxPercent && $product->enable_tax) {
            if (($product->price == $product->discount_price) && $product->taxable_price) {
                $product->tax = (($product->taxableprice * $taxPercent) / 100);
                $product->total = ($product->tax + $product->price);
            } else {
                $price = $product->discount_price ?? $product->price;
                $product->tax = round($price * $taxPercent / 100, 2);
            }
        } else {
            $product->tax = 0;
        }

        return $product->tax;
    }

    /**
     * @param Orders $order
     * @return Collection
     */
    public function calculateProductsDiscount(Orders $order): Collection
    {
        $products = $order->products()->select('products.id', 'products.name', 'products.price',
            'products.enable_tax', 'products.billing_type')->get();

        $productsCouponDiscounts = $this->calculateCouponsDiscount($order);

        $products->transform(function (Products $product) use ($order, $productsCouponDiscounts) {
            $product = $this->calculateProductDiscount($product, $order);
            $product->discount_price -= $productsCouponDiscounts[$product->id] ?? 0;
            $product->image_src = $product->getImageSrc();

            return $product;
        });

        return $products;
    }

    /**
     * @param Orders $order
     *
     * @return array
     */
    public function calculateCouponsDiscount(Orders $order)
    {
        $productsCouponDiscounts = [];

        $order->coupons()->each(
            function (Coupons $coupon) use ($order, &$productsCouponDiscounts) {
                $productsIds = $order->products()->whereHas(
                    'coupons',
                    function (Builder $query) use ($coupon) {
                        $query->whereKey($coupon->id);
                    }
                )->get();

                if ($coupon->type == Coupons::TYPE_CURRENCY) {
                    $productsIds->transform(
                        function ($product) use ($productsIds, $coupon, &$productsCouponDiscounts) {
                            $productsCouponDiscounts[$product->id] = ($productsCouponDiscounts[$product->id] ?? 0) + round(
                                    $coupon->discount / count($productsIds)
                                );
                        }
                    );
                } else {
                    if ($coupon->type == Coupons::TYPE_PERCENTS) {
                        $productsIds->transform(
                            function ($product) use ($productsIds, $coupon, &$productsCouponDiscounts) {
                                $productsCouponDiscounts[$product->id] = ($productsCouponDiscounts[$product->id] ?? 0) + ($product->price * $coupon->discount / 100);
                            }
                        );
                    }
                }
            }
        );

        $order->productSeatsUsed()->each(
            function (OrdersProductSeats $orderProductSeat) use ($order, &$productsCouponDiscounts) {
                if ($orderProduct = $orderProductSeat->orderProduct) {
                    $product = $order->products()->where('products.id', $orderProduct->product_id)->first();
                    if ($product) {
                        $productsCouponDiscounts[$product->id] = $product->price;
                    }
                }
            }
        );

        return $productsCouponDiscounts;
    }

    public function calculateProductsPrice(Orders $order)
    {
        $products = $this->calculateProductsDiscount($order);
        $products = $this->calculateProductsTax($products, $order->user);

        return $products;
    }
}
