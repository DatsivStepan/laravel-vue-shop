<?php

namespace App\Repositories;

use App\Models\Orders;
use App\Models\Products;
use App\Services\Sort\DefaultSort;
use App\Services\Sort\Sales\QuantitySort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Modules\Payments\Classes\PaymentMethodFactory;
use Modules\Payments\Repositories\PaymentMethodRepository;
use Nwidart\Modules\Facades\Module;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class OrdersRepository extends BaseRepository
{
    /**
     * @return Model
     */
    protected function getModelClass(): string
    {
        return Orders::class;
    }

    //TODO Need to refactoring
    /**
     * @param string $currency
     * @return array
     */
    public function getPaymentMethods(string $currency): array
    {
        $methods = [];

        if (getSetting('enable_invoice')) {
            $methods[] = [
                'id' => null,
                'name' => __('orders.invoice'),
                'method' => 'invoice',
                'settings' => [],
            ];

        }

        if (Module::has('Payments')) {
            $paymentRep = new PaymentMethodRepository();
            if ($models = $paymentRep->getPaymentsForOrders($currency)) {
                foreach ($models as $model) {
                    $paymentMethod = PaymentMethodFactory::create($model->getType());;
                    $methods[] = [
                        'id' => $model->id,
                        'name' => $model->getName(),
                        'method' => $model->getType(),
                        'settings' => $model->getSetting(),
                        'icon' => $paymentMethod->getIcon(),
                    ];
                }
            }
        }

        return $methods;
    }

    /**
     * Check that product is in order
     *
     * @param $orderId
     * @param $productId
     *
     * @return bool
     */
    public function isOrderHasProduct($orderId, $productId)
    {
        return ($this->getModelClass())::query()->where('id', $orderId)
            ->whereHas('products',
                function (Builder $query) use ($productId) {
                    $query->whereKey($productId);
                })->exists();
    }

    /**
     * @param Orders $order
     * @param array $products
     *
     * @return int
     */
    public function addProducts(Orders $order, array $products)
    {
        $result = $order->products()->syncWithoutDetaching($products);

        return count($result['attached']);
    }

    /**
     * @param Orders $order
     * @param int $discountId
     *
     * @return int
     */
    public function getProductsWithDiscount(Orders $order, int $discountId)
    {
        return $order->products()->whereHas('discounts',
            function (Builder $query) use ($discountId) {
                $query->whereKey($discountId);
            })->count();
    }

    /**
     * @param Orders $order
     */
    public function deleteEmptyDiscounts(Orders $order)
    {
        $productIds = $order->products()->pluck('products.id')->toArray();

        $order->discounts()->whereHas('products',
            function (Builder $query) use ($productIds) {
                $query->whereIn('id', $productIds);
            },
            '=',
            0)->detach();
    }

    /**
     * @param Orders $order
     * @param Products $product
     */
    public function addDiscountFromProductToOrder(Orders $order, Products $product)
    {
        $discounts = $product->activeDiscounts()->pluck('discounts.id');

        $order->discounts()->syncWithoutDetaching($discounts);
    }

    /**
     * @param null|User $user
     * @param null|int $perPage
     * @return AllowedFilter
     */
    public function getWithQuery($user = null, $perPage = null)
    {
        $filters = [
            'payment_status',
            AllowedFilter::scope('paid_on_between'),
            AllowedFilter::scope('search'),
            AllowedFilter::scope('filter_by_user'),
        ];

        $sorts = [
            'id', 'created_at', 'amount', 'subtotal',
            'payment_status', 'discount', 'status', 'note',
            AllowedSort::custom('quantity', new QuantitySort())->defaultDirection('desc'),
        ];

        return $this->getWithQueryBuilder(
            $filters,
            $sorts,
            $perPage,
            AllowedSort::custom('created_at', new DefaultSort())->defaultDirection('desc')
        );
    }

    /**
     * @param $externalID
     * @return Orders|null
     */
    public function findByExternalID($externalID)
    {
        return Orders::query()->where('external_id', $externalID)->first();
    }
}
