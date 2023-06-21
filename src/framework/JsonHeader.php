<?php
namespace Framework;

use Closure;
use Illuminate\Http\Request;

class JsonHeader
{
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
        $request->headers->set('Accept', 'application/json');

        /** @var \Illuminate\Http\Response $response */
        $response = $next($request);
        $response->headers->add([
            'Content-Type' => 'application/json; charset=UTF-8'
        ]);

        return $response;
    }
}
