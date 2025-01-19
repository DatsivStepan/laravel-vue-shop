<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class AuthController extends Controller
{
    /** @var UserRepository $repository */
    private $repository;

    /**
     * @param UserRepository $repository
     */
    public function __construct(UserRepository $repository)
    {
        $action = Route::currentRouteAction();
        if (($action == 'App\Http\Controllers\AuthController@refresh') &&
            ($connectionId = $this->getTokenPayload()['connection_id'])) {
            setTenant($connectionId);
        }

        $this->repository = $repository;
    }

    /**
     * @return array
     */
    private function getTokenPayload(): array
    {
        $token = auth()->setRequest(request())->parseToken();

        return app('tymon.jwt.provider.jwt')->decode($token->getToken()->get());
    }

    /**
     * Api Login
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        // TODO: get connection id
        /* switch database */

        $authToken = $request->get('auth_token');
        $connectionId = $request->get('connection_id');

        if ($connectionId) {
            setTenant($connectionId);

            if ($authToken && ($lmsUser = $this->repository->getLmsUserByToken($authToken))) {
                if ($token = Auth::login($lmsUser)) {
                    return response()
                        ->json(['status' => 'success'], 200)
                        ->header('Authorization', $token)
                        ->header('Access-Control-Allow-Headers', 'Authorization')
                        ->header('Access-Control-Expose-Headers', 'Authorization');
                }
            }
        }

        return response()->json(['error' => 'login_error'], 401);
    }

    /**
     * Api Logout
     * @return JsonResponse
     */
    public function logout()
    {
        $this->guard()->logout();

        return response()
            ->json([
                'status' => 'success',
                'msg' => 'Logged out Successfully.',
            ]);
    }

    /**
     * Get Auth User info
     *
     * @return JsonResponse
     */
    public function user()
    {
        $user = new UserResource($this->repository->find(auth()->id()));
        return response()
            ->json([
                'status' => 'success',
                'data' => $user,
            ]);
    }

    /**
     * Get Auth User info
     *
     * @return JsonResponse
     */
    public function refresh()
    {
        if ($token = $this->guard()->refresh()) {
            return response()
                ->json(['status' => 'successs'])
                ->header('Authorization', $token);
        }

        return response()->json(['error' => 'refresh_token_error'], 401);
    }

    /**
     * @return \Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard|Auth
     */
    private function guard()
    {
        return Auth::guard();
    }
}
