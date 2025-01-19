<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SetTenant
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws Exception
     */
    public function handle(Request $request, Closure $next)
    {
        if ($connectionId = $this->guard()->getPayload()->get('connection_id')) {
            setTenant($connectionId);
        }
    }

    /**
     * @return Auth
     */
    private function guard()
    {
        return Auth::guard();
    }
}
