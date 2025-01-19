<?php

namespace Modules\Payments\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Payments\Classes\PaymentMethodFactory;
use Modules\Payments\Entities\PaymentMethod;
use Modules\Payments\Events\PaymentCreated;
use Modules\Payments\Events\PaymentDeleted;
use Modules\Payments\Events\PaymentUpdated;
use Modules\Payments\Helpers\FunctionHelper;

class PaymentMethodRepository extends \App\Repositories\BaseRepository
{
    /**
     * @return PaymentMethod
     */
    protected function getModelClass(): string
    {
        return PaymentMethod::class;
    }

    /**
     * @param array $data
     * @return Model
     * @throws \Exception
     */
    public function create(array $data)
    {
        $model = parent::create($this->prepare($data));

        // Trigger event.
        event(new PaymentCreated($model));

        return $model;
    }

    /**
     * @param Model $model
     * @param array $data
     * @return Model
     * @throws \Exception
     */
    public function update($model, array $data)
    {
        $model = parent::update($model, $this->prepare($data));

        // Trigger event.
        event(new PaymentUpdated($model));

        return $model;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getPaymentTypes()
    {
        if ($methodsModels = FunctionHelper::getMethods()) {
            $res = [];
            foreach ($methodsModels as $modelPath) {
                $model = new $modelPath;
                $res[$model->getKey()] = $model->getName();
            }

            return $res;
        }

        return [];
    }

    // ACTIONS

    /**
     * @param array|int $ids
     * @return bool
     */
    public function hide($ids): bool
    {
        $ids = is_array($ids) ? $ids : [$ids];
        return $this->updateIn($ids, ['status' => PaymentMethod::STATUS_INACTIVE]);
    }

    /**
     * @param array|int $ids
     * @return bool
     */
    public function show($ids): bool
    {
        $ids = is_array($ids) ? $ids : [$ids];
        return $this->updateIn($ids, ['status' => PaymentMethod::STATUS_ACTIVE]);
    }
    // ACTIONS

    /**
     * @param Model $model
     * @return Model
     * @throws \Exception
     */
    public function deleted($model)
    {
        $model = parent::delete($model);

        // Trigger event.
        event(new PaymentDeleted($model));

        return $model;
    }

    /**
     * @param array $key
     * @return array
     * @throws \Exception
     */
    public function getPaymentMethodField($key): array
    {
        $paymentMethod = PaymentMethodFactory::create($key);

        return $paymentMethod->getFields();
    }

    /**
     * @param array $data
     * @return array
     */
    public function prepare($data): array
    {
        $data['status'] = (!is_null($data['status']) && (int)$data['status'] > 0) ? 1 : 0;
        return $data;
    }

    /**
     * @param array $ids
     * @param int $status
     * @return bool
     */
    public function changeVisible(array $ids, int $status)
    {
        return ($this->getModelClass())::query()
            ->whereIn('id', $ids)
            ->update(['status' => $status]);
    }


    /**
     * @param string $currency
     * @return Builder[]|Collection|PaymentMethod[]
     */
    public function getPaymentsForOrders(string $currency)
    {
        return ($this->getModelClass())::query()
            ->where('currency', $currency)
            ->where('status', PaymentMethod::STATUS_ACTIVE)
            ->get();
    }

    /**
     * @param [] $ids
     * @param int $status
     * @return bool
     */
    public function changeStatus($ids, $status)
    {
        return ($this->getModelClass())::query()
            ->whereIn('id', $ids)
            ->update(['status' => $status]);
    }
}
