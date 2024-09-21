<?php

namespace Orchestra\http;

/**
 * Class that handles Url Information
 * 
 * (c) @author
 * 
 * @author Creator-Solutions Owen Burns
 * @author Founder-Studios Owen Burns
 */
class UrlMatcher
{

    /**
     * @var array
     */
    private array $routes;

    /**
     * @var string
     */
    private string $callback;

    /**
     * @var string
     */
    private string $controller;

    /**
     * @var array
     */
    private array $serializableObject;


    public function __construct(array $routes = [])
    {
        $this->routes = $routes;
    }

    public function setRoutes(array $routes)
    {
        $this->routes = $routes;
    }

    public function serialize($api, $route)
    {
        $routeConfig = $this->routes[$api];

        for ($i = 0; $i < count($routeConfig); $i++) {
            $this->callback =  $routeConfig[$i]['_callback'];
            if ($this->callback === $route) {
                $this->controller = $routeConfig[$i]['_controller'];
                $this->serializableObject = ['_controller' => $this->controller, '_callback' => $this->callback];
                break;
            }
        }

        return $this->serializableObject;
    }

    public function serializeUrl(array $url = []): string
    {

        if (count($url) === 1) {
            return "/" . $url[0];
        } elseif (count($url) === 2) {
            return "/" . $url[1];
        } elseif (count($url) === 3) {
            return "/" . $url[2];
        } elseif (count($url) === 4) {
            return "/" . $url[3];
        } elseif (count($url) === 5) {
            return "/" . $url[3] . "/" . $url[4];
        }
    }
}
