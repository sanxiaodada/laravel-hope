<?php


namespace Framework;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    use Helper;

    public function getMorphClass()
    {
        return $this->getTable();
    }

    public static function getModelTable()
    {
        return static::query()->getModel()->getTable();
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }

}
