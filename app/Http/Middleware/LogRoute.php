<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class LogRoute
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response= $next($request);
        $log = [
            'uri' => $request->getUri(),
            'method' => $request->getMethod(),
           // 'body' => json_encode($request->all()),
            'response' => json_decode($response->getContent()),
            'ip' => $request->ip()
        ];
        DB::table('logs')->insert($log);
        Log::info(json_encode($log));
        return $response;
    }
}
