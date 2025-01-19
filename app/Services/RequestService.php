<?php

namespace App\Services;

use Illuminate\Http\Request;

class RequestService
{
    public $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function addToFilter($key, $value = '')
    {
        $requestData = $this->request->all();
        $filters = isset($requestData['filter']) ? $requestData['filter'] : [];
        $filters[$key] = $value;
        $this->request->request->add(['filter' => $filters]);
    }
}
