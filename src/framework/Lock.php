<?php
namespace Framework;


class Lock
{
    use Helper;

    const LOCK_PREFIX = 'lock:';

    /**
     * @param string $key
     * @param int $expire
     * @param int $uSleep
     * @return bool
     */
    public function lock(string $key, int $expire = 5, int $uSleep = 0) : bool {
        $redis = Hope::getRedis();
        $cacheKey = self::LOCK_PREFIX . $key;
        $start = time();

        do {
            $is_lock = $redis->setnx($cacheKey, time() + $expire);
            if (!$is_lock) {
                $lock_time = $redis->get($cacheKey);
                //锁已过期，重置
                if($lock_time < time()){
                    $this->unlock($key);
                    $is_lock = $redis->setnx($cacheKey, time()+$expire);
                }
            }

            if ($uSleep === 0 || $is_lock) {
                return $is_lock;
            }

            $uSleep = $uSleep < 20 ? 20 : $uSleep;
            usleep($uSleep);
        } while (time() - $start < $expire);

        return false;
    }

    /**
     * @param $key
     * @return int
     */
    public function unlock($key){
        $redis = Hope::getRedis();
        $cacheKey = self::LOCK_PREFIX . $key;
        return $redis->del($cacheKey);
    }
}
