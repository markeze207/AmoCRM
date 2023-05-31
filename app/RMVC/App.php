<?php

namespace App\RMVC;

use App\RMVC\Route\Route;
use App\RMVC\Route\RouteDispatcher;

class App
{
    // Запуск роутера
    public static function run()
    {

        $requestMethod = ucfirst(strtolower($_SERVER['REQUEST_METHOD']));

        $methodName = 'getRouters'.$requestMethod;

        foreach(Route::$methodName() as $routeConfiguration)
        {
            $routeDispatcher = new RouteDispatcher($routeConfiguration);
            $routeDispatcher->proccess();
        }
    }
}