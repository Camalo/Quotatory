<?php
namespace App\Model;

interface IAuthStorage
{
    public function login($login,  $password);

    public function getToken();

    public function updateToken($token);
}
