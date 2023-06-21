<?php
declare(strict_types=1);

namespace Framework;

use Illuminate\Contracts\Container\Container;
use Ray\Aop\Pointcut;
use Ytake\LaravelAspect\PointCut\CommonPointCut;
use Ytake\LaravelAspect\PointCut\PointCutable;

/**
 * Class LockablePointCut
 */
class LockablePointCut extends CommonPointCut implements PointCutable
{
    /** @var string */
    protected $annotation = Lockable::class;

    /**
     * @param Container $app
     *
     * @return \Ray\Aop\Pointcut
     */
    public function configure(Container $app): Pointcut
    {
        $interceptor = new LockableInterceptor();
        $this->setInterceptor($interceptor);

        return $this->withAnnotatedAnyInterceptor();
    }
}
