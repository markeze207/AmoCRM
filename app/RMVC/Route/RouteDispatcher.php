<?php

namespace App\RMVC\Route;

class RouteDispatcher
{

    private string $requestURI = '/';

    private array $paramMap = [];

    private array $paramRequestMap = [];

    private RouteConfiguration $routeConfiguration;

    /**
     * @param RouteConfiguration $routeConfiguration
     */
    public function __construct(RouteConfiguration $routeConfiguration)
    {
        $this->routeConfiguration = $routeConfiguration;
    }

    // Запуск всех процессов
    public function proccess()
    {
        // Если строка запроса есть, то мы чистим ее и сохраняем
        $this->saveRequestURI();

        // Сохраняем в массив параметры
        $this->setParamMap();

        // Приводим параметры в строке запроса в регулярное выражение
        $this->makeRegexRequest();

        // Запускаем
        $this->run();

    }

    // Сохранение чистого URI
    private function saveRequestURI()
    {
        if($_SERVER['REQUEST_URI'] !== '/')
        {
            $this->requestURI = $this->clean($_SERVER['REQUEST_URI']);
            $this->routeConfiguration->route = $this->clean($this->routeConfiguration->route);
        }

    }
    // Очистка URI
    private function clean($str): string
    {
        return preg_replace('/(^\/)|(\/$)/', '', $str);
    }

    // Сохранение параметров
    private function setParamMap()
    {
        $routeArray = explode('/', $this->routeConfiguration->route);

        foreach($routeArray as $paramKey => $param)
        {
            if(preg_match('/{.*}/',$param))
            {
                $this->paramMap[$paramKey] = preg_replace('/(^{)|(}$)/','', $param);
            }
        }
    }

    // Приводим параметры из строки в регулярное выражение
    private function makeRegexRequest()
    {
        $requestUriArray = explode('/', $this->requestURI);

        foreach($this->paramMap as $paramKey => $param)
        {
            if(!isset($requestUriArray[$paramKey]))
            {
                return;
            }
            $this->paramRequestMap[$param] = $requestUriArray[$paramKey];
            $requestUriArray[$paramKey] = '{.*}';
        }
        $this->requestURI = implode('/',$requestUriArray);
        $this->prepareRegex();

    }

    // Подготовка параметров
    private function prepareRegex()
    {
        $this->requestURI = str_replace('/', '\/', $this->requestURI);
    }

    // Запуск роутера
    private function run()
    {
        if(preg_match("/$this->requestURI/", $this->routeConfiguration->route))
        {
            $this->render();
        }
    }

    // Рендер класса
    private function render()
    {

        $ClassName = $this->routeConfiguration->controller;
        $action = $this->routeConfiguration->action;

        print((new $ClassName)->$action(...$this->paramRequestMap));

        die();
    }

}