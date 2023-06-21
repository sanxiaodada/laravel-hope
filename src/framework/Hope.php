<?php
namespace Framework;


use Illuminate\Redis\Connections\PhpRedisConnection;
use Illuminate\Support\Facades\Redis;

class Hope
{
    public static function isProduct(): bool
    {
        $env = env('APP_ENV', 'local');
        if ($env === 'prod') {
            return true;
        }
        return false;
    }

    public static function getRedis(string $connection = 'default'): PhpRedisConnection
    {
        /** @var PhpRedisConnection $redis */
        $redis = Redis::connection($connection);
        return $redis;
    }

    public static function getLoginId(): int
    {
        $jwt = app(Jwt::class);
        $user_id = $jwt->checkToken();
        if ($user_id === false) {
            return 0;
        }

        return $user_id;
    }

    /**
     * 获取随机邀请码
     * @return string
     */
    public static function makeInviterCode() {
        $code = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $rand = $code[rand(0,25)]
            .strtoupper(dechex(date('m')))
            .date('d').substr(time(),-5)
            .substr(microtime(),2,5)
            .sprintf('%02d',rand(0,99));
        for(
            $a = md5( $rand, true ),
            $s = '0123456789ABCDEFGHIJKLMNOPQRSTUV',
            $d = '',
            $f = 0;
            $f < 6;
            $g = ord( $a[ $f ] ),
            $d .= $s[ ( $g ^ ord( $a[ $f + 6 ] ) ) - $g & 0x1F ],
            $f++
        );
        return $d;
    }


}
