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
        
        $log = PHP_EOL."Api Log ===============
        Duration: ".number_format($this->end - $this->start, 3)."\n
        Url: ".$request->fullUrl()."\n
        Method: ".$request->getMethod()."\n
        IP Address: ".$request->getClientIp()."\n
        Status Code: ".$response->getStatusCode()."\n
        Request: ".json_encode($request->all())."\n
        Response: ".$response->content()."Api Log Ends ===========".PHP_EOL;
                
        \Log::channel('api_log')->info($log);
        // \Log::channel('api_log')->info('ApiLog ===========================').'\n';
        // \Log::channel('api_log')->info('Duration:  ' .number_format($this->end - $this->start, 3)).'\n';
        // \Log::channel('api_log')->info('URL: ' . $request->fullUrl()).'\n';
        // \Log::channel('api_log')->info('Method: ' . $request->getMethod()).'\n';
        // \Log::channel('api_log')->info('IP Address: ' . $request->getClientIp()).'\n';
        // \Log::channel('api_log')->info('Status Code: ' . $response->getStatusCode()).'\n';
        // \Log::channel('api_log')->info('Request: ' . json_encode($request->all())).'\n';
        // \Log::channel('api_log')->info('Response:' . $response->content());
    }
}
