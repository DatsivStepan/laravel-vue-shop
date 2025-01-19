<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Admin\CategoriesRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoriesController extends Controller
{
    private $repository;

    /**
     * @param CategoriesRepository $repository
     */
    public function __construct(CategoriesRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function getTree(Request $request)
    {
        return $this->repository->getTree();
    }
}
