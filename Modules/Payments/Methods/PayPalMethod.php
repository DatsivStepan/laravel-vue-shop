<?php

namespace Modules\Payments\Methods;

use Modules\Payments\Helpers\FunctionHelper;

class PayPalMethod extends AbstractMethod implements MethodInterface
{
    /**
     * @var string
     */
    private $key = 'PayPal';

    /**
     * @var string
     */
    private $icon = 'logo.png';

    /**
     * @var string
     */
    private $name = 'PayPal';

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return '/storage/images/payment-methods/paypal/' . $this->icon;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return [
            FunctionHelper::prepareMethodField('text', 'client_id', __('payments.form.client_id')),
            FunctionHelper::prepareMethodField('text', 'secret', __('payments.form.secret')),
            FunctionHelper::prepareMethodField('select', 'mode', __('payments.form.mode'), $this->getModeArray()),
        ];
    }
}
