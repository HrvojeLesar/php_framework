<?php

namespace Hrvoje\PhpFramework\Router;

class Router
{
    private array $routes;
    private Route $default_route;

    public function __construct()
    {
        $this->routes = [];
        $this->default_route = new Route("/not-found", Method::Get, function () {
            echo "<h1>Not Found</h1>";
        });
    }

    public function addRoute(Route $route)
    {
        $this->routes[] = $route;
        return $this;
    }

    public function resolve()
    {
        return $this->tryFindRoute()->resolve();
    }

    private function tryFindRoute(): Route
    {
        $path = $_SERVER["PATH_INFO"];
        $method = $_SERVER["REQUEST_METHOD"];

        if (is_null($path)) {
            $path = "/";
        }

        foreach ($this->routes as &$route) {
            if ($route->match($path, $method)) {
                return $route;
            }
        }

        return $this->default_route;
    }
}
