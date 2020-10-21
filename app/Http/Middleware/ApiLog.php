<?php

namespace App\Http\Middleware;

use Closure;

class ApiLog
{
    private $start;
    private $end;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->start = microtime(true);
        return $next($request);
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return mixed
     */
    public function terminate($request, $response)
    {
        $this->end = microtime(true);
        $this->log($request, $response);
    }

    protected function log($request, $response)
    {
        
        \Log::channel('api_log')->info('ApiLog done===========================');
        \Log::channel('api_log')->info('Duration:  ' .number_format($this->end - $this->start, 3));
        \Log::channel('api_log')->info('URL: ' . $request->fullUrl());
        \Log::channel('api_log')->info('Method: ' . $request->getMethod());
        \Log::channel('api_log')->info('IP Address: ' . $request->getClientIp());
        \Log::channel('api_log')->info('Status Code: ' . $response->getStatusCode());
        \Log::channel('api_log')->info('Response: ' . $response->content());
    }
}
