<?php


namespace Framework;

use ReflectionClassConstant;
use Ytake\LaravelAspect\Annotation\Cacheable;

abstract class BaseConstants
{
    public static function getAnnotations(array $classConstants)
    {
        $result = [];
        /** @var ReflectionClassConstant $classConstant */
        foreach ($classConstants as $classConstant) {
            $code = $classConstant->getValue();
            $docComment = $classConstant->getDocComment();
            if ($docComment && (is_int($code) || is_string($code))) {
                $result[$code] = static::parse($docComment, $result[$code] ?? []);
            }
        }

        return $result;
    }

    protected static function parse(string $doc, array $previous = [])
    {
        $pattern = '/\\@(\\w+)\\(\\"(.+)\\"\\)/U';
        if (preg_match_all($pattern, $doc, $result)) {
            if (isset($result[1], $result[2])) {
                $keys = $result[1];
                $values = $result[2];

                foreach ($keys as $i => $key) {
                    if (isset($values[$i])) {
                        $previous[\Str::lower($key)] = $values[$i];
                    }
                }
            }
        }

        return $previous;
    }

    public static function getConstants(): array
    {
        $class = new \ReflectionClass(static::class);
        return $class->getConstants();
    }

    public static function getConstantsAnnotations(): array
    {
        $class = new \ReflectionClass(static::class);

        $classConstants = $class->getReflectionConstants();
        $list = static::getAnnotations($classConstants);
        return $list;
    }

    public static function getMessage($key)
    {
        $res = self::getConstantsAnnotations();
        return $res[$key]['message'];
    }

    public static function getType($key)
    {
        $res = self::getConstantsAnnotations();
        return $res[$key]['type'];
    }

    public static function getKey($key)
    {
        $res = self::getConstantsAnnotations();
        return $res[$key]['key'];
    }
}
