<?php

namespace App\Model;

use App\Class\Database;

class AuthStorageBd implements IAuthStorage
{
    private Database $database;

    public function __construct()
    {
        $this->database = new Database();
    }

    public function login($login,  $password)
    {
        $this->database->connect();

        $data = $this->database->select(
            "SELECT
                `tokenPassword`
            FROM
                `tokens`
            WHERE
                `tokenLogin` = :login",
            ':login',
            $login
        );


        if (!isset($data[0]['tokenPassword'])) {
            return false;
        }

        return password_verify($password, $data[0]['tokenPassword']);
    }
    public function getToken()
    {
        $this->database->connect();

        $data = $this->database->query(
            "SELECT
                `tokenHash`
            FROM
                `tokens`
            WHERE
                `tokenExpireTo` >= NOW()"
        );
       
        return isset($data[0]["tokenHash"]) ? $data[0]["tokenHash"] : false;
    }

    public function updateToken($token)
    {
        return $this->database->update(
            "UPDATE
                `tokens`
            SET
                `tokenHash` = :token,
                `tokenExpireTo` = DATE_ADD(NOW(), INTERVAL `tokenLifetime` SECOND)",
            ':token',
            $token
        );
    }
}
