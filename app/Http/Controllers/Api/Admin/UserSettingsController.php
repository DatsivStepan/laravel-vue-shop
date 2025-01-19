<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\User\UserSettingsService;
use Glorand\Model\Settings\Exceptions\ModelSettingsException;
use Illuminate\Http\Request;

class UserSettingsController extends Controller
{
    /** @var UserSettingsService $service */
    public $service;

    public function __construct(UserSettingsService $service)
    {
        $this->service = $service;
    }

    /**
     * @param Request $request
     *
     * @throws ModelSettingsException
     */
    public function selectCurrency(Request $request)
    {
        $request->validate([
            'code' => 'required|string|exists:currencies,code',
        ]);

        $settings = $this->service->selectCurrency($request->user(), $request->code);

        return response()->json($settings);
    }
}
