<?php

namespace App\Class;

class Json
{

    public static function geJsonData()
    {
        $path = $_SERVER['DOCUMENT_ROOT'] . '/App/config.json';

        $jsonData = file_get_contents($path);
        $data = json_decode($jsonData, true);
        return $data;
    }
}
