<?php

namespace App\Services\Admin;

use App\Classes\Enum\Order\BillingType;
use App\Models\Products;
use App\Repositories\Admin\Products\InstallmentRepository;
use App\Repositories\Admin\ProductsRepository;
use Illuminate\Support\Arr;

class ProductService
{
    /**
     * @var ProductsRepository
     */
    private $repository;
    /**
     * @var InstallmentRepository
     */
    private $installmentRepository;

    public function __construct(ProductsRepository $repository, InstallmentRepository $installmentRepository)
    {
        $this->repository = $repository;
        $this->installmentRepository = $installmentRepository;
    }

    /**
     * @param array $data
     * @return Products
     * @throws \Exception
     */
    public function create(array $data): Products
    {
        $productData = Arr::except($data, ['installment']);

        /** @var Products $product */
        $product = $this->repository->create($productData);

        if (isset($data['installment'])) {
            $this->installmentRepository->create($product->id, $data['installment']);
        }

        return $product;
    }

    /**
     * @param Products $product
     * @param array $data
     * @return Products
     * @throws \Exception
     */
    public function update(Products $product, array $data): Products
    {
        $productData = Arr::except($data, ['installment']);

        $product = $this->repository->update($product, $productData);

        if ($product->billing_type === BillingType::INSTALLMENT && isset($data['installment'])) {
            $this->createInstallment($product, $data['installment']);
        }

        return $product;
    }

    /**
     * @param Products $product
     * @return float
     */
    public function calculateProductDiscount(Products $product): float
    {
        $discountPercent = $this->getDiscountPercent($product);

        return round($product->price * $discountPercent / 100, 2);
    }

    /**
     * @param Products $product
     * @return int|mixed
     */
    private function getDiscountPercent(Products $product)
    {
        return $product->activeDiscountsAnyType()->sum('discount');
    }

    private function createInstallment(Products $product, array $installment)
    {
        if ($product->installment) {
            $product->installment->update($installment);
        } else {
            $product->installment()->create($installment);
        }
    }
}
