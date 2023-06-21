<?php


namespace Framework;


use Closure;
use Illuminate\Http\Request;

class AuthMiddleware
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
        $jwt = app(Jwt::class);
        $check_result = $jwt->checkToken();
        if (!$check_result){
            $this->errorUnauthorized();
        }
        return $next($request);
    }
}
