<?php

namespace Modules\Payments\Methods;

use Modules\Payments\Helpers\FunctionHelper;

class AuthorizeMethod extends AbstractMethod implements MethodInterface
{
    /**
     * @var string
     */
    private $key = 'authorize';

    /**
     * @var string
     */
    private $icon = 'logo.png';

    /**
     * @var string
     */
    private $name = 'Authorize';

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
        return '/storage/images/payment-methods/authorize/' . $this->icon;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return [
            FunctionHelper::prepareMethodField('text', 'login_id', __('payments.form.authorize.login_id')),
            FunctionHelper::prepareMethodField('text', 'transaction_key',
                __('payments.form.authorize.transaction_key')),
            FunctionHelper::prepareMethodField('text', 'public_client_key',
                __('payments.form.authorize.public_client_key')),
            FunctionHelper::prepareMethodField('select', 'mode', __('payments.form.mode'), $this->getModeArray()),
        ];
    }
}
