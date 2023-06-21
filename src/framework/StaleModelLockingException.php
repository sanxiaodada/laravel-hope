<?php


namespace Framework;

class StaleModelLockingException extends ApiException {
    protected $message = "数据库乐观锁出错";

    /**
     * @var string
     */
    protected $table;

    public function getTable()
    {
        return $this->table;
    }

    public function __construct(string $table)
    {
        parent::__construct(411, $this->message);

        $this->table= $table;
    }
}
