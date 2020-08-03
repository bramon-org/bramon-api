<?php

namespace App\Traits;

use Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Encryption\EncryptException;

/**
 * Used to encrypt/decrypt Eloquent Model properties.
 * Any properties listed in the $encryptable array
 * will be automatically encrypted when set, and
 * decrypted when accessed, or when the model
 * is converted toJson() or toArray().
 *
 * Encryption is handled by the Crypt helper function, which
 * uses the cipher/key defined in config/app.php.
 *
 * Class Encryptable
 * @package Encryptable
 */
trait Encryptable
{

    /**
     * @param $key
     *
     * @return bool
     */
    public function encryptable($key)
    {
        return in_array($key, $this->encryptable);
    }


    /**
     * Decrypt a value.
     *
     * @param $value
     *
     * @return string
     */
    protected function decryptAttribute($value)
    {
        try {
            if (isset($value)) {
                $value = decrypt($value);
            }
        } catch (DecryptException $decryptException) {
            // do nothing
        }

        return $value;
    }


    /**
     * Encrypt a value.
     *
     * @param $value
     *
     * @return string
     */
    protected function encryptAttribute($value)
    {
        try {
            if (isset($value)) {
                $value = encrypt($value);
            }
        } catch (EncryptException $encryptException) {
            // do nothing
        }

        return $value;
    }


    /**
     * Extend the Eloquent method so properties present in
     * $encrypt are decrypted when directly accessed.
     *
     * @param $key  The attribute key
     *
     * @return string
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if ($this->encryptable($key) && isset($value)) {
            $value = $this->decryptAttribute($value);
        }

        return $value;
    }


    /**
     * Extend the Eloquent method so properties present in
     * $encrypt are encrypted whenever they are set.
     *
     * @param $key      The attribute key
     * @param $value    Attribute value to set
     *
     * @return mixed
     */
    public function setAttribute($key, $value)
    {
        if ($this->encryptable($key) && isset($value)) {
            $value = $this->encryptAttribute($value);
        }

        return parent::setAttribute($key, $value);
    }


    /**
     * Extend the Eloquent method so properties in
     * $encrypt are decrypted when toArray()
     * or toJson() is called.
     *
     * @return mixed
     */
    public function getArrayableAttributes()
    {
        $attributes = parent::getArrayableAttributes();

        foreach ($attributes as $key => $attribute) {
            if ($this->encryptable($key)) {
                $attributes[$key] = $this->decryptAttribute($attribute);
            }
        }

        return $attributes;
    }
}
