<?php

namespace App\Services\Admin\Discount;


use App\Models\Discount;
use App\Repositories\Admin\DiscountRepository;
use App\Repositories\Admin\ProductsRepository;
use App\Repositories\Admin\VendorsRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class DiscountService implements \App\Services\Admin\DiscountService
{
    /** @var DiscountRepository $repository */
    private $repository;
    /** @var ProductsRepository $productRepository */
    private $productRepository;
    /** @var VendorsRepository $vendorRepository */
    private $vendorRepository;

    public function __construct(
        DiscountRepository $repository,
        ProductsRepository $productRepository,
        VendorsRepository $vendorRepository
    ) {
        $this->repository = $repository;
        $this->productRepository = $productRepository;
        $this->vendorRepository = $vendorRepository;
    }

    /**
     * Assign products on discounts
     *
     * @param int $discountID
     * @param array $products
     * @param bool $assignAll
     *
     * @return Discount
     */
    public function assignProducts(int $discountID, array $products, bool $assignAll = false): Discount
    {
        /** @var Discount $discount */
        $discount = $this->repository->getOneOrFail($discountID);

        if ($assignAll) {
            $products = $this->productRepository->getAllProductIds()->toArray();
        }

        $discount->products()->syncWithoutDetaching($products);

        return $discount->load('products');
    }

    /**
     * detach product
     *
     * @param int $discountID
     * @param int|array $productID
     */
    public function detachProduct(int $discountID, $productID)
    {
        /** @var Discount $discount */
        $discount = $this->repository->getOneOrFail($discountID);

        $discount->products()->detach($productID);
    }

    /**
     * Assign vendors on discount
     *
     * @param int $discountID
     * @param array $vendors
     * @param bool $assignAll
     *
     * @return Discount
     */
    public function assignVendors(int $discountID, array $vendors, bool $assignAll = false): Discount
    {
        /** @var Discount $discount */
        $discount = $this->repository->getOneOrFail($discountID);

        if ($assignAll) {
            $vendors = $this->vendorRepository->getAllVendorsIds()->toArray();
        }

        $discount->vendors()->syncWithoutDetaching($vendors);

        return $discount->load('vendors');
    }

    /**
     * Get available products
     *
     * @param int $discountID
     *
     * @return Collection
     */
    public function getProductForSelect(int $discountID)
    {
        /** @var Discount $discount */
        $discount = $this->repository->getOneOrFail($discountID);

        $usedProducts = $discount->products()->pluck('products.id')->toArray();

        return $this->productRepository->getProductsWithout($usedProducts);
    }

    /**
     * Get assigned products
     *
     * @param int $discountID
     * @param int $perPage
     *
     * @param array $filters
     *
     * @return LengthAwarePaginator
     */
    public function getAssignedProducts(int $discountID, int $perPage, array $filters = [])
    {
        /** @var Discount $discount */
        $discount = $this->repository->getOneOrFail($discountID);

        $query = $discount->products()->select('products.id', 'products.name', 'products.created_at');

        if (isset($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }

        return $query->paginate($perPage);
    }
}
