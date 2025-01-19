<?php

namespace Modules\Payments\Transformers;

use App\Helpers\GridDataHelper;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;
use Modules\Payments\Classes\PaymentMethodFactory;

class PaymentsCollection extends ResourceCollection
{
    public $user;

    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Support\Collection
     */
    public function toArray($request)
    {
        $this->user = Auth::user();

        return $this->collection->map(function ($collection) {
            $pResource = (new PaymentResource($collection))
                ->toArray(request());
            $paymentMethod = PaymentMethodFactory::create($collection->getType());;
            return [
                'name' => $pResource['name'],
                'type' => $paymentMethod->getName(),
                'icon' => $paymentMethod->getIcon(),
                'actions' => $this->getActions($pResource),
                'index' => $pResource['id'],
            ];
        });
    }

    /**
     * @param App\Http\Resources\Admin\PaymentResource $record
     * @return array
     */
    public function getActions($record)
    {
        $actions = [];
        if ($this->user->can('managePayments')) {

            $actions[] = GridDataHelper::generateEditButton(
                'admin-payments-edit',
                $record['id'],
                __('system.edit')
            );

            $sAction = $record['status'] ? 'hide' : 'show';
            $actions[] = GridDataHelper::generateActionApi(__('system.' . $sAction),
                route('api.payments.actions', $record['id']),
                [
                    'action' => $sAction,
                    'status' => $record['status'],
                ]);

            if ($this->user->can('manageVendors')) {
                $actions[] = GridDataHelper::generateActionLink(
                    __('vendors.assignvendors'),
                    'admin-payments-vendors-assigns',
                    ['id' => $record['id']]
                );
            }

            $actions[] = GridDataHelper::generateDeleteButton(
                '/admin/payments/' . $record['id'],
                __('system.delete')
            );
        }

        return $actions;
    }
}
