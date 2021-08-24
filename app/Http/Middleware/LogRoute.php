<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\DB;
use Monolog\Logger;

class LogRoute
{

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);


        $log = [
            'uri' => $request->getUri(),
            'method' => $request->getMethod(),
            'body' => json_encode($request->all()),
            'response' => $response->getStatusCode(),
            'ip' => $request->getClientIp(),
            'created_at' => Carbon::now()
        ];
        DB::table('logs')->insert($log);

        return $response;
    }
}
