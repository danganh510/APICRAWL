<?php
namespace Score\Utils;

use Phalcon\Crypt;
use Phalcon\Mvc\User\Component;

class PasswordGenerator extends Component
{
    //generate password
    public static function salt($lenght=12)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $lenght; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    //generate password
    public function generatePassword($length=10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ~!@#$%^&*()_+<>?,./;:|{}[]';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        $password = $this->security->hash($randomString);
        return $password;
    }
    /**
     * @param $data
     * @return string
     */
    public static function encryptToken($data) {
        $crypt = new Crypt();
        $crypt->setCipher('aes-256-ctr');
        $crypt->useSigning(true);
        $key = "T2\xb1\x88\xe8\xc9\xde\\\x9c\xbe\x54\x19&[\x50\xe8\xa4~Lc1\xbeW\x9b";

        return $crypt->encryptBase64((string)$data, $key);
    }

    /**
     * @param $data
     * @return bool|false|string
     */
    public static function decryptToken($data) {
        try {
            $crypt = new Crypt();
            $crypt->setCipher('aes-256-ctr');
            $crypt->useSigning(true);
            $key = "T2\xb1\x88\xe8\xc9\xde\\\x9c\xbe\x54\x19&[\x50\xe8\xa4~Lc1\xbeW\x9b";
            return $crypt->decryptBase64($data, $key);
        } catch (\Exception $exception) {
            return false;
        }
    }
}

