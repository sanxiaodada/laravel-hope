<?php


namespace Framework;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Redis\Connections\PhpRedisConnection;
use Illuminate\Support\Facades\Redis;
use InvalidArgumentException;
use zhangyongku\Hope\Constants\StatusCodeConstants;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait Helper
{
    public function retError($error,
                             int $code = 400,
                             array $data = null,
                             array $headers = [])
    {
        if (is_string($error)) {
            $error = new ErrorResponse($error, $code, $data);
        }

        if (!($error instanceof ErrorResponse)) {
            throw new InvalidArgumentException('error must instanceof ErrorResponse');
        }

        throw new HttpResponseException(response($error, 400, $headers));
    }

    /**  http 商场专用
     * @param $error
     * @param int $code
     * @param array|null $data
     * @param array $headers
     */
    public function httpError($error,
                             int $code,
                             array $data = null,
                             array $headers = [])
    {
        if (is_string($error)) {
            $error = new ErrorResponse($error, $code, $data);
        }

        if (!($error instanceof ErrorResponse)) {
            throw new InvalidArgumentException('error must instanceof ErrorResponse');
        }

        throw new HttpResponseException(response($error, $code, $headers));
    }

    public function errorNotFound(string $message = 'Not Found')
    {
        throw new HttpResponseException(response(new ErrorResponse($message, 404), 404));
    }

    public function errorUnauthorized()
    {
        throw new HttpResponseException(response(new ErrorResponse('Unauthorized', 401), 401));
    }

    public function errorDataError()
    {
        throw new HttpException(421, 'data error!!!');
    }

    /**
     * 系统繁忙
     * @param string|null $message
     */
    protected function errorBusy(string $message = null)
    {
        throw new HttpException(420, '系统繁忙');
    }
}
