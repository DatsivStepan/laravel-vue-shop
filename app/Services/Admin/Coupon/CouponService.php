<?php

namespace App\Services\Admin\Coupon;

use App\Models\Coupons;
use App\Repositories\Admin\CouponsRepository;
use App\Repositories\Admin\ProductsRepository;
use App\Repositories\Admin\VendorsRepository;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class CouponService implements \App\Services\Admin\CouponService
{
    /** @var int Coupon code length */
    private const CODE_LENGTH = 16;

    /** @var int Section size for code example for 4 ****-**** */
    private const SECTION_SIZE = 4;

    /** @var CouponsRepository $repository */
    private $repository;

    /** @var ProductsRepository $productRepository */
    private $productRepository;

    /** @var VendorsRepository $vendorRepository */
    private $vendorRepository;

    public function __construct(
        CouponsRepository $repository,
        ProductsRepository $productRepository,
        VendorsRepository $vendorRepository
    ) {
        $this->repository = $repository;
        $this->productRepository = $productRepository;
        $this->vendorRepository = $vendorRepository;
    }


    /**
     * Used for create coupon
     *
     * @param array $data
     *
     * @return Coupons
     * @throws Exception
     *
     */
    public function create(array $data): Coupons
    {
        $data['code'] = $data['code'] ?? $this->generateCode();

        return $this->repository->create($data);
    }

    /**
     * Assign products on coupon
     *
     * @param int $couponID
     * @param array $products
     * @param bool $assignAll
     *
     * @return Coupons
     */
    public function assignProducts(int $couponID, array $products, bool $assignAll = false): Coupons
    {
        /** @var Coupons $coupon */
        $coupon = $this->repository->getOneOrFail($couponID);

        if ($assignAll) {
            $products = $this->productRepository->getAllProductIds()->toArray();
        }

        $coupon->products()->syncWithoutDetaching($products);

        return $coupon->load('products');
    }

    /**
     * Assign vendors on coupon
     *
     * @param int $couponID
     * @param array $vendors
     * @param bool $assignAll
     *
     * @return Coupons
     */
    public function assignVendors(int $couponID, array $vendors, bool $assignAll = false): Coupons
    {
        /** @var Coupons $coupon */
        $coupon = $this->repository->getOneOrFail($couponID);

        if ($assignAll) {
            $vendors = $this->vendorRepository->getAllVendorsIds()->toArray();
        }

        $coupon->vendors()->syncWithoutDetaching($vendors);

        return $coupon->load('vendors');
    }

    /**
     * Generate coupon code before create coupon
     *
     * @param int|null $len
     *
     * @param int|null $sectionSize
     *
     * @return string
     */
    public function generateCode(int $len = null, int $sectionSize = null): string
    {
        $len = $len ?? self::CODE_LENGTH;
        $sectionSize = $sectionSize ?? self::SECTION_SIZE;


        $code = $this->buildCode($len, $sectionSize);
        $existsCodes = $this->repository->getAllCodesOfCoupons();

        while (true) {
            if ($existsCodes->contains('code', $code)) {
                $code = $this->buildCode($len, $sectionSize);
            } else {
                break;
            }
        }

        return $code;
    }

    /**
     * Build code for generateCode method
     *
     * @param int $len
     * @param int $sectionSize
     *
     * @return string
     */
    private function buildCode(int $len, int $sectionSize): string
    {
        $code = "";

        for ($i = 0; $i < intdiv($len, $sectionSize); $i++) {
            if ($i) {
                $code .= "-";
            }

            $code .= Str::random($sectionSize);
        }

        return Str::upper($code);
    }

    /**
     * Get products which can be selected in coupon
     *
     * @param int $discountID
     *
     * @return Collection
     */
    public function getProductForSelect(int $discountID)
    {
        /** @var Coupons $coupon */
        $coupon = $this->repository->getOneOrFail($discountID);

        $usedProducts = $coupon->products()->pluck('products.id')->toArray();

        return $this->productRepository->getProductsWithout($usedProducts);
    }

    /**
     * Get products which were assign on coupon
     *
     * @param int $couponId
     * @param int $perPage
     *
     * @param array $filters
     *
     * @return LengthAwarePaginator
     */
    public function getAssignedProducts(int $couponId, int $perPage, array $filters)
    {
        /** @var Coupons $coupon */
        $coupon = $this->repository->getOneOrFail($couponId);

        $query = $coupon->products()->select('products.id', 'products.name', 'products.created_at');

        if (isset($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }

        return $query->paginate($perPage);
    }

    /**
     * detach product from coupon
     *
     * @param int $discountID
     * @param int|array $productID
     */
    public function detachProduct(int $discountID, $productID)
    {
        /** @var Coupons $coupon */
        $coupon = $this->repository->getOneOrFail($discountID);

        $coupon->products()->detach($productID);
    }
}
