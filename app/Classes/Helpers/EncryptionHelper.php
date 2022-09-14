<?php


namespace App\Classes\Helpers;


use Defuse\Crypto\Crypto;
use Illuminate\Support\Facades\Crypt;

class EncryptionHelper
{
    /**
     * @param string $text
     * @param string $key
     * @return string
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     */
    public static function encrypt(string $text, string $key): string
    {
        return Crypt::encryptString(Crypto::encryptWithPassword($text, $key));
    }

    /**
     * @param string $cryptotext
     * @param $key
     * @return string
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     * @throws \Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException
     */
    public static function decrypt(string $cryptotext, $key) : string
    {
        return Crypto::decryptWithPassword(
            Crypt::decryptString($cryptotext),
            $key
        );
    }
}
