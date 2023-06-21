<?php


namespace Framework;

use Closure;
use Illuminate\Http\Request;

class CorsMiddleware
{
    use Helper;
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods:PUT,GET,POST,DELETE,OPTIONS,HEAD');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Headers:DNT,X-CustomHeader,Keep-Alive,UserRepository-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type,Authorization');
        return $next($request);
    }
}
