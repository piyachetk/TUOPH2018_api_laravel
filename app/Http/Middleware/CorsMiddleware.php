<?php

namespace App\Http\Middleware;

class CorsMiddleware {
    public function handle($request, \Closure $next)
    {
        $response = $next($request);
        if (method_exists($response, 'header')){
            $response->header('Access-Control-Allow-Methods', 'HEAD, GET, POST, PUT, PATCH, DELETE, OPTIONS');
            $response->header('Access-Control-Allow-Headers', $request->header('Access-Control-Request-Headers'));
            $response->header('Access-Control-Allow-Origin', '*');
            $response->header('Access-Control-Allow-Credentials', 'true');
        }
        return $response;
    }
}