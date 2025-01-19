<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCustomer;
use App\Http\Resources\Admin\CustomerResource;
use App\Repositories\Admin\CustomerRepository;
use Illuminate\Http\Response;

class CustomerController extends Controller
{
    private $repository;

    /**
     * @param CustomerRepository $repository
     */
    public function __construct(CustomerRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show(int $id)
    {
        return new CustomerResource($this->repository->getOne($id, 'user_id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCustomer $request
     * @param int $id
     * @return Response
     */
    public function update(UpdateCustomer $request, int $id)
    {
        $consumer = $this->repository->getOne($id);

        return new CustomerResource($this->repository->update($consumer, $request->all()));
    }
}
