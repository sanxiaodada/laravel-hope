<?php


namespace Framework;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseRequest extends FormRequest
{
    use Helper;

    public function __call($method, $parameters)
    {
        return $this->input($method, $parameters[0] ?? '');
    }


}
