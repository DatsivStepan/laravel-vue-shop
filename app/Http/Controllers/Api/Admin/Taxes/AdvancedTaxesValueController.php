<?php

namespace App\Http\Controllers\Api\Admin\Taxes;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Taxes\AdvancedTaxesFieldResource;
use App\Http\Resources\Admin\Taxes\AdvancedTaxesValueCollection;
use App\Http\Resources\Admin\Taxes\AdvancedTaxesValueResource;
use App\Models\Taxes\AdvancedTaxesValue;
use App\Repositories\Admin\Taxes\AdvancedTaxesFieldRepository;
use App\Repositories\Admin\Taxes\AdvancedTaxesValueRepository;
use Illuminate\Http\Request;

class AdvancedTaxesValueController extends Controller
{
    /**
     * @var AdvancedTaxesValueRepository
     */
    private $repository;
    /**
     * @var AdvancedTaxesFieldRepository
     */
    private $fieldRepository;

    public function __construct(AdvancedTaxesValueRepository $repository, AdvancedTaxesFieldRepository $fieldRepository)
    {
        $this->repository = $repository;
        $this->fieldRepository = $fieldRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $request->validate([
            'field_id' => 'required|exists:advanced_taxes_fields,id',
        ]);

        $values = $this->repository->paginate($request->field_id, request('par_page'));
        $field = $this->fieldRepository->find($request->field_id);
        return (new AdvancedTaxesValueCollection($values, $field))->response();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $payload = $request->validate([
            'field_id' => 'required|exists:advanced_taxes_fields,id',
            'value' => 'required|string',
            'tax' => 'required|numeric|min:0',
        ]);

        $field = $this->repository->create($payload);

        return (new AdvancedTaxesFieldResource($field))->response();
    }

    /**
     * Display the specified resource.
     *
     * @param AdvancedTaxesValue $value
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(AdvancedTaxesValue $value)
    {
        return (new AdvancedTaxesValueResource($value))->response();
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param AdvancedTaxesValue $value
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'ids' => 'required',
        ]);

        $result = $this->repository->delete($request->ids);

        return response($result, 204);
    }
}
