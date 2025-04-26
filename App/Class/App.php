<?php

namespace App\Class;

class App
{
    protected static $instance;
    private $router;

    public static function init()
    {
        if (is_null(self::$instance))
            self::$instance = new App;
        return self::$instance;
    }

    public function getPage()
    {
        $this->router = new Router();
      
        if ($controller = $this->router->getController($_SERVER["REQUEST_URI"])) {
            
            $method = $this->router->getMethod();

            if (method_exists($controller, $method)) {
                $controller->$method();
            } else {
                header("HTTP/1.0 404 Not Found");
                echo '404 Not Found 1';
            }
        } else {
            header("HTTP/1.0 404 Not Found");
            echo '404 Not Found 2';
        }
    }
}
