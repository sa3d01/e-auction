<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Exception;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;

class CheckApiToken
{

    /**
     * @param $request
     * @param Closure $next
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $user=auth()->userOrFail();
            if ($user->status==0){
                $response = [
                    'status' => 401,
                    'message' => 'تم حظرك من قبل إدارة التطبيق ..',
                ];
                return response()->json($response, 401);
            }
        }catch (UserNotDefinedException $e){
            $response = [
                'status' => 401,
                'message' => 'انتهت جلستك يرجى تسجيل الدخول',
            ];
            return response()->json($response, 401);
        }
        return $next($request);
    }
}
