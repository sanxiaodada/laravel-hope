<?php


namespace Framework;


use Closure;
use Illuminate\Http\Request;

class TestMiddleware
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
        if (Hope::isProduct()) {
            $this->errorUnauthorized();
        }
        return $next($request);
    }
}
