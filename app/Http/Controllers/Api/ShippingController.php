<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UpdateShipping;
use App\Http\Resources\ProductSubscribeResource;
use App\Http\Resources\ShippingResource;
use App\Repositories\Admin\ShippingRepository;
use Illuminate\Http\Request;

class ShippingController
{
    /**
     * @var ShippingRepository
     */
    private $repository;

    public function __construct(ShippingRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Request $request
     * @return ProductSubscribeResource|null
     */
    public function getShippingData(Request $request)
    {
        $data = $request->validate([
            'order_id' => ['required'],
            'user_id' => ['required'],
        ]);

        $userId = $data['user_id'];
        $options = [
            'filters' => [
                'order_id' => $data['order_id']
            ],
            'whereHas' => [
                [
                    'table' => 'order',
                    'query' => function ($query) use ($userId) {
                        return $query->where('user_id', $userId);
                    }
                ]
            ]
        ];
        $shipping = $this->repository->getOneWithOptions($options);

        if (!$shipping) {
            unset($options['filters']);
            if ($shipping = $this->repository->getOneWithOptions($options)) {
                $shipping->id = null;
                $shipping->order_id = $data['order_id'];
            }
        }

        return $shipping ? (new ShippingResource($shipping)) : [];
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param UpdateShipping $request
     * @return Response
     */
    public function store(UpdateShipping $request)
    {
        $request->validated();

        return $this->repository->create($request->all());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateShipping $request
     * @param int $id
     * @return Response
     */
    public function update(UpdateShipping $request, int $id)
    {
        $shipping = $this->repository->getOne($id);

        return new ShippingResource($this->repository->update($shipping, $request->all()));
    }
}
