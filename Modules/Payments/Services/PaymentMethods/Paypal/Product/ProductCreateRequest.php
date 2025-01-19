<?php


namespace Modules\Payments\Services\PaymentMethods\Paypal\Product;


use PayPalHttp\HttpRequest;

class ProductCreateRequest extends HttpRequest
{
    public function __construct()
    {
        parent::__construct("/v1/catalogs/products?", "POST");
        $this->headers["Content-Type"] = "application/json";
    }

    public function buildRequestBody(array $params)
    {
        $this->body = $params;
    }
}
