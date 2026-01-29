<?php

namespace App\Support;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

final class AccountSecretCrypto
{
    public static function encrypt(string $plaintext): string
    {
        return Crypt::encryptString($plaintext);
    }

    public static function decrypt(string $ciphertext): string
    {
        try {
            return Crypt::decryptString($ciphertext);
        } catch (DecryptException $e) {
            throw $e;
        }
    }
}
