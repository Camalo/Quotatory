<?php

namespace App\Class;
use App\Controller;

class Router
{
    private $requestUrl = [];

    /**
     * Вернуть экземпляр класса контроллера или false, если такого контроллера не существует
     *  
     * @param string SERVER REQUEST URI
     * @return object|false
     */
    public function getController($requestUrl = false)
    {
        $this->setRequestUrl($requestUrl);

        if (count($this->requestUrl) == 0) {
            return false;
        }
        
        $controllerName = (!empty($this->requestUrl[0])) ? ucfirst($this->requestUrl[0]) : "Index";

        $controllerPath =  "App\Controller\\" .   $controllerName . 'Controller';

        if (class_exists($controllerPath)) {
            return new $controllerPath();
        } else {
            return false;
        }
    }

    /**
     * Вернуть название метода, если метод не передан в $requestUrl, то вернуть название метода по умолчанию.
     * 
     * @param string SERVER REQUEST URI
     * @return string|false
     */
    public function getMethod($requestUrl = false)
    {
        $this->setRequestUrl($requestUrl);

        if (count($this->requestUrl) == 0) {
            return false;
        }

        $method = (isset($this->requestUrl[1])) ? $this->requestUrl[1] : "index";
        return $method;
    }

    /**
     * Вернуть параметры запроса
     * @param string SERVER REQUEST URI
     * @return array
     */
    public function getParams($requestUrl = false)
    {
        return [];
    }

    private function setRequestUrl($requestUrl)
    {
        // Должно генерировать если передан $requestUrl
        // ИЛИ
        // Должно генерировать если не установлено $this->$requestUrl
        if (count($this->requestUrl) == 0 || $requestUrl) {
            $path =  trim($requestUrl, '/\\');
            $this->requestUrl = explode("/", $path);
        }
    }
}
