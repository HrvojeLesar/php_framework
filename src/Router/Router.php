<?php

namespace Hrvoje\PhpFramework\Router;

use Hrvoje\PhpFramework\Request\RequestInterface;

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

    public function addRoute(Route $route): Router
    {
        $this->routes[] = $route;
        return $this;
    }

    public function resolve(RequestInterface $request): mixed
    {
        return $this->tryFindRoute($request)->resolve();
    }

    private function tryFindRoute(RequestInterface $request): Route
    {
        /** @var Route $route */
        foreach ($this->routes as &$route) {
            if ($route->match($request->getRequestUrl(), $request->getRequestMethod())) {
                return $route;
            }
        }

        return $this->default_route;
    }
}
