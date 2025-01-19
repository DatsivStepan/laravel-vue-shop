<?php


namespace Modules\Payments\Methods;


class AbstractMethod
{
    const MODE_SANDBOX = 0;
    const MODE_PRODUCTION = 1;

    /**
     * @return array
     */
    public function getModeArray(): array
    {
        return [
            self::MODE_SANDBOX => __('payments.sandbox'),
            self::MODE_PRODUCTION => __('payments.production'),
        ];
    }
}
