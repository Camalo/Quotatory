<?php

namespace App\Controller;

use App\Model\AuthStorageStrategy;
use App\Model\AuthStorageBd;
use App\Model\QuotesHandler;


class QuotesController
{
    private $modelInstance;
    public function __construct()
    {
        $this->modelInstance = new QuotesHandler();
    }
    
    //А почему может быть протектед
    public function getToken()
    {
        if (!$this->getPostItem('login') || !$this->getPostItem('password')) {
            $response = ['error' => ['code' => 2, 'message' => 'Неправильная пара логин/пароль',]];
        } else {

            $auth = new AuthStorageStrategy(new AuthStorageBd());

            if (!$auth->login($this->getPostItem('login'), $this->getPostItem('password'))) {
                $response = ['error' => ['code' => 4, 'message' => 'Неправильная пара логин/ключ']];
            } else {
                $token = $auth->generateToken();
                $auth->updateToken($token);

                $response = ['data' => ['token' => $token]];
            }
        }
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    public function getAll()
    {
        if (!$xToken = $this->getHeaderItem('x-Token')) {
            $response = ['error' => ['code' => 2, 'message' => 'Для этого метода необходима авторизация по токену в заголовке',]];
        } else {

            $auth = new AuthStorageStrategy(new AuthStorageBd());

            if ($xToken != $auth->getToken()) {
                $response = ['error' => ['code' => 3, 'message' => 'Токен неправильный или просрочен']];
            } else {

                $response = ['data' => $this->modelInstance->getQuotes()];
            }
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    public function getAuthors()
    {
        if (!$xToken = $this->getHeaderItem('x-Token')) {
            $response = ['error' => ['code' => 2, 'message' => 'Для этого метода необходима авторизация по токену в заголовке',]];
        } else {

            $auth = new AuthStorageStrategy(new AuthStorageBd());

            if ($xToken != $auth->getToken()) {
                $response = ['error' => ['code' => 3, 'message' => 'Токен неправильный или просрочен']];
            } else {

                $response = ['data' => $this->modelInstance->getAuthors()];
            }
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    public function getById()
    {
        if (!$xToken = $this->getHeaderItem('x-Token')) {
            $response = ['error' => ['code' => 2, 'message' => 'Для этого метода необходима авторизация по токену в заголовке',]];
        } else {

            $auth = new AuthStorageStrategy(new AuthStorageBd());

            if ($xToken != $auth->getToken()) {
                $response = ['error' => ['code' => 3, 'message' => 'Токен неправильный или просрочен']];
            } else {
                $response = ['data' => $this->modelInstance->getQuote($this->getGetItem('id'))];
            }
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    public function voteForQuote()
    {
        if (!$xToken = $this->getHeaderItem('x-Token')) {
            $response = ['error' => ['code' => 2, 'message' => 'Для этого метода необходима авторизация по токену в заголовке',]];
        } else {

            $auth = new AuthStorageStrategy(new AuthStorageBd());

            if ($xToken != $auth->getToken()) {
                $response = ['error' => ['code' => 3, 'message' => 'Токен неправильный или просрочен']];
            } else {

                if ($this->modelInstance->voteForQuote($this->getPostItem('quoteId'))) {
                    $response = ['data' => 'success'];
                } else {
                    $response = ['error' => ['code' => 10, 'message' => 'Не правильно передан quoteId']];
                }
            }
        }
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    private function getHeaderItem($key)
    {
        if (!$headers = getallheaders()) {
            return false;
        }

        return isset($headers[$key]) ? $headers[$key] : false;
    }
    private function getPostItem($key)
    {
      
        //Нарушение единства ответсвенности?
        if (!$post = file_get_contents("php://input")) {
            return false;
        }
        $postParams = json_decode($post, true);
        return isset($postParams[$key]) ? $postParams[$key] : false;
    }

    private function getGetItem($key)
    {
        //Нарушение единства ответсвенности?
        if (!$get = $_GET) {
            return null;
        }
        return isset($get[$key]) ? $get[$key] : null;
    }
}
