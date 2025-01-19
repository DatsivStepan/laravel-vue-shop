<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProductFavorite;
use App\Http\Resources\ProductFavoritesCollection;
use App\Repositories\Admin\ProductFavoriteRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductFavoritesController extends Controller
{
    private $repository;

    /**
     * @param ProductFavoriteRepository $repository
     */
    public function __construct(ProductFavoriteRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(CreateProductFavorite $request)
    {
        $request->validated();
        return $this->repository->create($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required',
            'user_id' => 'required'
        ]);

        return $this->repository->delete($this->repository->getOneByConditions($data));
    }
}
