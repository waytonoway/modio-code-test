<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class AddSwaggerConfigToRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $documentation = Config::get('l5-swagger.default');
        $swaggerConfig = Config::get('l5-swagger.documentations.'.$documentation);

        $request->merge([
            'documentation' => $documentation,
            'config' => array_merge(Config::get('l5-swagger.defaults'), $swaggerConfig)
        ]);

        return $next($request);
    }
}
