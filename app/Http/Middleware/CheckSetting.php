<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class CheckSetting
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string $settingKey
     * @return mixed
     * @throws Exception
     */
    public function handle(Request $request, Closure $next, $settingKey)
    {
        if (!getSetting($settingKey)) {
            throw new HttpResponseException(
                response()->json(['errors' => 'Coupons not enabled'], 401)
            );
        }
        return $next($request);
    }
}
