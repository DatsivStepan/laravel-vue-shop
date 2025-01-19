<?php

namespace Modules\Payments\Http\Controllers;

use App\Classes\Enum\Order\PaymentType;
use App\Helpers\FunctionHelper;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Payments\Entities\PaymentMethod;
use Modules\Payments\Http\Requests\CreatePaymentRequest;
use Modules\Payments\Http\Requests\UpdatePaymentRequest;
use Modules\Payments\Repositories\PaymentMethodRepository;
use Modules\Payments\Transformers\PaymentResource;
use Modules\Payments\Transformers\PaymentsCollection;

class PaymentsController extends Controller
{
    /** @var PaymentMethodRepository $repository */
    private $repository;

    /**
     * @param PaymentMethodRepository $repository
     */
    public function __construct(PaymentMethodRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $requestData = $request->all();
        $options = [];
        if (!empty($requestData['sort'])) {
            $sort = json_decode($requestData['sort']);

            $options['sort'] = [
                'field' => $sort->column,
                'direction' => $sort->reverse ? 'desc' : 'asc'
            ];
        }

        return new PaymentsCollection($this->repository->paginate(getDefaultPaginateCount(), $options));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreatePaymentRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function store(CreatePaymentRequest $request)
    {
        $request->validated();

        $data = $request->all();
        $data['currency'] = request('currency', getSetting('currency'));

        return response()->json($this->repository->create($data));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return new PaymentResource($this->repository->getOne($id, 'id', true));
    }

    /**
     * @param Request $request
     * @return array
     * @throws Exception
     */
    public function actions(Request $request, $id)
    {
        $data = $request->validate([
            "action" => "required|string",
        ]);

        if (method_exists($this->repository, $data['action'])) {
            return [
                'status' => $this->repository->{$data['action']}($id)
            ];
        }

        throw new Exception("Action '" . $data['action'] . "' not exist");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdatePaymentRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdatePaymentRequest $request, $id)
    {
        $request->validated();

        $data = $request->all();
        $data['currency'] = request('currency', getSetting('currency'));

        $item = $this->repository->getOne($id);

        return (new PaymentResource($this->repository->update($item, $data)))->response();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $item = $this->repository->getOne($id);

        return $this->repository->deleted($item);
    }

    /**
     * @param string $pageType
     * @return Response
     */
    public function getOptions(string $pageType)
    {
        $currencies = Currency::query()
            ->select('code', 'name')
            ->where('active', 1)
            ->pluck('name', 'code');

        $res = [];
        $res['statuses'] = FunctionHelper::arrayToVueOptions(PaymentMethod::$statusOptions);
        // Temporary added, after added currency methods
        // Use $this->getCurrencies();
        $res['currencies'] = FunctionHelper::arrayToVueOptions($currencies);
        $res['paymentTypes'] = FunctionHelper::arrayToVueOptions([
            PaymentType::PAYPAL => __('payments.methods.' . PaymentType::PAYPAL),
            PaymentType::STRIPE => __('payments.methods.' . PaymentType::STRIPE),
            PaymentType::AUTHORIZE => __('payments.methods.' . PaymentType::AUTHORIZE),
        ]);

        return $res;
    }

    /**
     * @param string $type
     * @return Response
     */
    public function getFieldSettings(string $type)
    {
        return $this->repository->getPaymentMethodField($type);
    }
}
