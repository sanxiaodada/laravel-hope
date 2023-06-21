<?php

namespace Framework;

trait ModelEncryptionTrait
{
    /**
     * 是否加密
     * @param string $key
     * @return bool
     */
    public function isEncryptable(string $key) : bool
    {
        if (!isset($this->encryptable)) {
            return false;
        }
        return in_array($key, $this->encryptable);
    }

    //SELECT AES_DECRYPT(UNHEX(phone),SUBSTR(UNHEX(SHA2('zhangyongku',512)),1,15)) FROM users;
    private function getAESKey() : string
    {
        $key = env('DB_AES_KEY', 'zhangyongku');
        $key = hex2bin(openssl_digest($key, 'sha512'));
        $key = substr($key, 0, 15);

        return $key;
    }

    /**
     * 解密
     * @param string $value
     * @return string
     */
    protected function decryptAttribute(string $value) : string
    {
        if ($value) {
            $value = openssl_decrypt(base64_encode(hex2bin($value)), 'aes-128-ecb', $this->getAESKey());
        }
        return $value;
    }

    /**
     * 加密
     * @param string $value
     * @return string
     */
    protected function encryptAttribute(string $value) : string
    {
        if ($value) {
            $value = bin2hex(base64_decode(openssl_encrypt($value, 'aes-128-ecb', $this->getAESKey())));
        }
        return $value;
    }



    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);
        if ($this->isEncryptable($key)) {
            $value = $this->decryptAttribute($value);
        }
        return $value;
    }

    public function setAttribute($key, $value)
    {
        if ($this->isEncryptable($key)) {
            $value = $this->encryptAttribute($value);
        }
        return parent::setAttribute($key, $value);
    }

    public function getArrayableAttributes()
    {
        $attributes = parent::getArrayableAttributes();
        foreach ($attributes as $key => $attribute) {
            if ($this->isEncryptable($key)) {
                $attributes[$key] = $this->decryptAttribute($attribute);
            }
        }
        return $attributes;
    }
}
