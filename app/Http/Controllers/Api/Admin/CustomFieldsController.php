<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\FunctionHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCustomFields;
use App\Http\Resources\Admin\CustomFieldResource;
use App\Http\Resources\Admin\CustomFieldsCollection;
use App\Models\CustomFields;
use App\Repositories\Admin\CustomFieldsRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CustomFieldsController extends Controller
{
    private $repository;

    /**
     * @param CustomFieldsRepository $repository
     */
    public function __construct(CustomFieldsRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        return new CustomFieldsCollection($this->repository->getWithQuery(getDefaultPaginateCount()), Auth::user());
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show(int $id)
    {
        return new CustomFieldResource($this->repository->getOne($id, 'id', true));
    }

    /**
     * @param Request $request
     * @param string $action
     * @return array
     */
    public function bulkActions(Request $request, string $action)
    {
        $data = $request->validate([
            "ids" => "required|array",
        ]);

        if (method_exists($this->repository, $action)) {

            switch ($action) {
                case 'delete':
                    $status = $this->repository->deleteAll($data['ids']);
                    break;
                default:
                    $status = $this->repository->{$action}($data['ids']);
            }

            return [
                'status' => $status,
            ];
        }

        throw new Exception("Action '" . $action . "' not exist");
    }

    /**
     * @param Request $request
     * @param int $fieldId
     * @return Response
     */
    public function actions(Request $request, int $fieldId)
    {
        $data = $request->validate([
            "action" => "required|string",
        ]);

        if (method_exists($this->repository, $data['action'])) {
            return [
                'status' => $this->repository->{$data['action']}($fieldId)
            ];
        }

        throw new Exception("Action '" . $data['action'] . "' not exist");
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function getFieldsForForm(Request $request)
    {
        return $this->repository->getFieldsForForm($request['instanceType'], $request['instanceId']);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function reloadSortOrder(Request $request)
    {
        $requestData = $request->all();
        return [
            'status' => $this->repository->reloadSortOrder($requestData['items'], 'custom_fields')
        ];
    }

    /**
     * @param string $pageType
     * @return Response
     */
    public function getOptions(string $pageType)
    {
        if ($pageType == 'edit') {
            $fieldTypes = CustomFields::getFieldTypes();
            $requiredOptions = CustomFields::$requiredOptions;

            return [
                'fieldTypes' => FunctionHelper::arrayToVueOptions($fieldTypes),
                'requiredOptions' => FunctionHelper::arrayToVueOptions($requiredOptions),
                'typesWithOptions' => CustomFields::$typesWithOptions
            ];
        }

        return [];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, int $id)
    {
        $customField = $this->repository->getOne($id);

        return new CustomFieldResource($this->repository->update($customField, $request->all()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(CreateCustomFields $request)
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
    public function destroy(int $id)
    {
        return $this->repository->delete($this->repository->getOne($id));
    }
}
