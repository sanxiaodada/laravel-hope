<?php


namespace Framework;

class SecretModel extends BaseModel
{
    use OptimisticLockingTrait;

    /**
     * @var string 标识字段名
     */
    public $idFieldName = 'user_id';
    /**
     * @var string 签名字段名
     */
    public $signFieldName = 'sign';

    protected $hidden = ['lock_version', 'sign'];

    public function getSignData() : array
    {
        return [];
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }
}
