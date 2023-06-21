<?php

namespace Framework;

class ErrorResponse
{
    /**
     * @var int
     */
    protected $code = 400;
    /**
     * @var string
     */
    protected $message = 'error';
    /**
     * @var array
     */
    protected $data = [];

    public function __construct(string $message,
                                int $code = 400,
                                array $data = null)
    {
        $this->code = $code;
        $this->message = $message;
        $this->data = $data;
    }

    public function __toString(): string
    {
        if ($this->data === null) {
            $this->data = new \stdClass();
        }
        return json_encode([
            'code' => $this->code,
            'message' => $this->message,
            'data' => $this->data
        ]);
    }
}
