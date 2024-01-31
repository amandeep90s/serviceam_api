<?php

namespace App\Traits;

trait Encryptable
{
    protected string $dbSecret;
    protected string $encryptionSalt;
    protected string $encryptionIv;

    public function __construct()
    {
        $this->dbSecret = env('DB_SECRET');
        $this->encryptionSalt = env('ENCRYPTION_SALT');
        $this->encryptionIv = env('ENCRYPTION_IV');
    }

    public function getAttribute($key): mixed
    {
        $value = parent::getAttribute($key);
        if (in_array($key, $this->encryptable) && $value !== '') {
            $value = $this->customDecrypt($value, $this->dbSecret);
        }

        return $value;
    }

    protected function customDecrypt($encrypted_string, $passphrase): bool|string
    {
        $cipherText = base64_decode($encrypted_string);
        $iterations = 999; //same as js encrypting

        $key = hash_pbkdf2("sha512", $passphrase, $this->encryptionSalt, $iterations, 64);

        return openssl_decrypt($cipherText, 'aes-256-cbc', hex2bin($key), OPENSSL_RAW_DATA, $this->encryptionIv);
    }

    public function setAttribute($key, $value): mixed
    {
        if (in_array($key, $this->encryptable)) {
            $value = $this->customEncrypt($value, $this->dbSecret);
        }
        return parent::setAttribute($key, $value);
    }

    protected function customEncrypt($pure_string, $passphrase): string
    {
        $iterations = 999;
        $key = hash_pbkdf2("sha512", $passphrase, $this->encryptionSalt, $iterations, 64);

        $encrypted_data = openssl_encrypt($pure_string, 'aes-256-cbc', hex2bin($key), OPENSSL_RAW_DATA, $this->encryptionIv);

        return base64_encode($encrypted_data);
    }

    public function attributesToArray(): array
    {
        $attributes = parent::attributesToArray();
        foreach ($this->encryptable as $key) {
            if (isset($attributes[$key])) {
                $attributes[$key] = $this->customDecrypt($attributes[$key], $this->dbSecret);
            }
        }
        return $attributes;
    }
}
