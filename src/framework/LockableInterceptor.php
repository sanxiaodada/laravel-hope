<?php
declare(strict_types=1);

namespace Framework;

use Ray\Aop\MethodInvocation;

use Ytake\LaravelAspect\Interceptor\AbstractCache;
use function is_array;

/**
 * Class LockableInterceptor
 */
class LockableInterceptor extends AbstractCache
{
    use Helper;
    /**
     * @param MethodInvocation $invocation
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function invoke(MethodInvocation $invocation)
    {
        /** @var Lockable $annotation */
        $annotation = $invocation->getMethod()->getAnnotation($this->annotation)
            ?? new $this->annotation([]);
        $keys = $this->generateCacheName($annotation->cacheName, $invocation);
        if (!is_array($annotation->key)) {
            $annotation->key = [$annotation->key];
        }
        $keys = $this->detectCacheKeys($invocation, $annotation, $keys);
        $key = $this->recursiveImplode($this->join, $keys);

        $lock = app(Lock::class);

        $uSleep = 0;
        if ($annotation->isWait) {
            $uSleep = $annotation->uSleep < 20 ? 20 : $annotation->uSleep;
        }

        $flg = $lock->lock($key, $annotation->lifetime, $uSleep);
        if (!$flg) {
            $this->errorBusy();
        }

        try {
            $result = $invocation->proceed();
        } finally {
            $lock->unlock($key);
        }

        return $result;
    }
}
