<?php

namespace App\Traits;

trait Encryptable
{
    /**
     * If the attribute is in the encryptable array
     * then decrypt it.
     *
     * @param  $key
     *
     * @return false|mixed|string $value
     */
    public function getAttribute($key): mixed
    {
        $value = parent::getAttribute($key);
        if (in_array($key, $this->encryptable) && $value !== '') {
            $value = $this->customDecrypt($value, env('DB_SECRET'));
        }

        return $value;
    }

    /**
     * Returns decrypted original string
     */
    protected function customDecrypt($encrypted_string, $passphrase): bool|string
    {

        $salt = env('ENCRYPTION_SALT');
        $iv = env('ENCRYPTION_IV');
        $ciphertext = base64_decode($encrypted_string);
        $iterations = 999; //same as js encrypting

        $key = hash_pbkdf2("sha512", $passphrase, $salt, $iterations, 64);

        return openssl_decrypt($ciphertext, 'aes-256-cbc', hex2bin($key), OPENSSL_RAW_DATA, $iv);
    }

    /**
     * If the attribute is in the encryptable array
     * then encrypt it.
     *
     * @param $key
     * @param $value
     * @return mixed
     */
    public function setAttribute($key, $value): mixed
    {
        if (in_array($key, $this->encryptable)) {
            $value = $this->customEncrypt($value, env('DB_SECRET'));
        }
        return parent::setAttribute($key, $value);
    }

    /**
     * Returns an encrypted & utf8-encoded
     */
    protected function customEncrypt($pure_string, $passphrase): string
    {

        $salt = env('ENCRYPTION_SALT');
        $iv = env('ENCRYPTION_IV');

        $iterations = 999;
        $key = hash_pbkdf2("sha512", $passphrase, $salt, $iterations, 64);

        $encrypted_data = openssl_encrypt($pure_string, 'aes-256-cbc', hex2bin($key), OPENSSL_RAW_DATA, $iv);


        return base64_encode($encrypted_data);
    }

    /**
     * When need to make sure that we iterate through
     * all the keys.
     *
     * @return array
     */
    public function attributesToArray(): array
    {
        $attributes = parent::attributesToArray();
        foreach ($this->encryptable as $key) {
            if (isset($attributes[$key])) {
                $attributes[$key] = $this->customDecrypt($attributes[$key], env('DB_SECRET'));
            }
        }
        return $attributes;
    }
}
