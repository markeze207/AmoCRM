<?php

namespace App\RMVC\Route;

class Route
{

    private static array $routersGet = [];

    private static array $routersPost = [];


    // Получение списка роутеров get
    public static function getRoutersGet(): array
    {
        return self::$routersGet;
    }

    // Получение списка роутеров post
    public static function getRoutersPost(): array
    {
        return self::$routersPost;
    }

    // get route
    public static function get(string $route, array $controller): RouteConfiguration
    {

        $routeConfiguration = new RouteConfiguration($route, $controller[0], $controller[1]);
        self::$routersGet[] = $routeConfiguration;
        return $routeConfiguration;
        
    }

    // post route
    public static function post(string $route, array $controller): RouteConfiguration
    {

        $routeConfiguration = new RouteConfiguration($route, $controller[0], $controller[1]);
        self::$routersPost[] = $routeConfiguration;
        return $routeConfiguration;

    }

    // redirect
    public static function redirect($url)
    {
        header('Location: '.$url);
    }

}