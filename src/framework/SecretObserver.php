<?php


namespace Framework;


class SecretObserver
{
    use Helper;

    public function retrieved(SecretModel $model)
    {
        $data = $model->getSignData();
        if (count($data) === 0) {
            return;
        }
        $signFieldName = $model->signFieldName;

        $sign = $this->getSign($data, (string)$model->{$model->idFieldName});
        $salt = config('hope.secret-salt', 'zhangyongku');
        if (!$model->$signFieldName) {
            $this->errorDataError();
        }

        $str = '$2y$10$'.strtolower(md5($sign . $salt));
        if ($str != $model->$signFieldName) {
            $this->errorDataError();
        }
    }

    public function saving(SecretModel $model)
    {
        $data = $model->getSignData();
        if (count($data) === 0) {
            return;
        }
        $signFieldName = $model->signFieldName;

        $sign = $this->getSign($data, (string)$model->{$model->idFieldName});
        $salt = config('hope.secret-salt', 'zhangyongku');
        $model->$signFieldName = '$2y$10$'.strtolower(md5($sign . $salt));
    }

    private function getSign(array $data, string $sign_key)
    {
        ksort($data);

        $code = '';

        foreach ($data as $key => $val) {
            $code .= $key . ':' . $val . '|';
        }

        $code = '|' . $sign_key . '|' . $code;

        $code = md5($code);
        $result = strtoupper($code);

        return $result;
    }
}
