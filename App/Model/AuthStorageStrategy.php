<?php

namespace App\Model;

class AuthStorageStrategy
{

    public function __construct(private IAuthStorage $strategy) {}

    public function login($login, $password)
    {
       return $this->strategy->login($login,  $password);
    }

    public function getToken()
    {
        return $this->strategy->getToken();
    }
    public function updateToken($token)
    {
        $this->strategy->updateToken($token);
    }

    public function generateToken()
    {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }
        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }
}
