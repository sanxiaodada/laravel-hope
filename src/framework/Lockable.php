<?php
declare(strict_types=1);

namespace Framework;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target("METHOD")
 */
class Lockable extends Annotation
{
    /** @var null|string[] $value cache key */
    public $key = null;

    /** @var null */
    public $cacheName = null;

    /** @var int $lifetime cache life time */
    public $lifetime = 120;

    /**
     * 是否等待
     * @var bool
     */
    public $isWait = false;

    /**
     * 等待时间
     * @var int
     */
    public $uSleep = 200;
}
