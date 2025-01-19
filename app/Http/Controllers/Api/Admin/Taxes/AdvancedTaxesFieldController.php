<?php

namespace App\Http\Controllers\Api\Admin\Taxes;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Taxes\AdvancedTaxesFieldCollection;
use App\Http\Resources\Admin\Taxes\AdvancedTaxesFieldResource;
use App\Models\Taxes\AdvancedTaxesField;
use App\Repositories\Admin\Taxes\AdvancedTaxesFieldRepository;
use Illuminate\Http\Request;

class AdvancedTaxesFieldController extends Controller
{
    /**
     * @var AdvancedTaxesFieldRepository
     */
    private $repository;

    public function __construct(AdvancedTaxesFieldRepository $repository)
    {

        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $fields = $this->repository->paginate(request('par_page'));

        return (new AdvancedTaxesFieldCollection($fields))->response();
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
            'key' => 'required|string',
            'name' => 'required|string',
        ]);

        $field = $this->repository->create($payload);

        return (new AdvancedTaxesFieldResource($field))->response();
    }

    /**
     * Display the specified resource.
     *
     * @param AdvancedTaxesField $field
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(AdvancedTaxesField $field)
    {
        return (new AdvancedTaxesFieldResource($field))->response();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'ids' => 'required',
        ]);

        $result = $this->repository->delete($request->ids);

        return response($result, 204);
    }

    public function getOptions()
    {
        $user = auth()->user();
        $fields = $user->getFields();

        $result = [];

        foreach ($fields as $field) {
            $item['name'] = __("users.$field");
            $item['key'] = $field;

            $result[] = $item;
        }

        return response()->json($result);
    }
}
