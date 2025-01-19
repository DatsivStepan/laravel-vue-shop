<?php

namespace Modules\Payments\Methods;

use Modules\Payments\Helpers\FunctionHelper;

class StripeMethod extends AbstractMethod implements MethodInterface
{
    /**
     * @var string
     */
    private $key = 'stripe';

    /**
     * @var string
     */
    private $icon = 'logo.png';

    /**
     * @var string
     */
    private $name = 'Stripe';

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
        return '/storage/images/payment-methods/stripe/' . $this->icon;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return [
            FunctionHelper::prepareMethodField('text', 'publishable_key', __('payments.form.publishable_key')),
            FunctionHelper::prepareMethodField('text', 'secret_key', __('payments.form.secret')),
        ];
    }
}
