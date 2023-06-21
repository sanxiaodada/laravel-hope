<?php

namespace Framework;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;

abstract class BaseController extends Controller
{
    use AuthorizesRequests, DispatchesJobs;
    use Helper;

    /**
     * 获取授权用户ID
     */
    public function getAuthId() : int
    {
        return Hope::getLoginId();
    }

    /**
     * 是否登录
     * @return bool
     */
    public function isLogin() : bool
    {
        return !!$this->getAuthId();
    }

    public function retSuccess($data, array $meta = null, array $headers = [],$code=200)
    {
        if ($meta === null) {
            $meta = new \stdClass();
        }

        $ret = compact('data', 'meta','code');

        if (!Hope::isProduct() && PrintSql::$sql) {
            $ret['_sql'] = PrintSql::$sql;
        }

        throw new HttpResponseException(response(json_encode($ret), 200, $headers));
    }


}
