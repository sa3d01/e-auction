<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Monolog\Logger;

class LogRoute
{

    private $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

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

        $this->logger->info('Dump request', [
            'request' => serialize($request),
            'response' => serialize($response),
        ]);

        $log = [
            'uri' => $request->getUri(),
            'method' => $request->getMethod(),
            'body' => json_encode($request->all()),
            'response' => $response->getStatusCode(),
            'ip' => $request->ip()
        ];
        DB::table('logs')->insert($log);

        return $response;
    }

    public function terminate($request, $response)
    {
        $log = [
            'uri' => $request->getUri(),
            'method' => $request->getMethod(),
            'body' => json_encode($request->all()),
            'response' => $response->getStatusCode(),
            'ip' => $request->ip()
        ];
        DB::table('logs')->insert($log);
    }

}
