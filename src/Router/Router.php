<?php

namespace Hrvoje\PhpFramework\Router;

use Hrvoje\PhpFramework\Request\RequestInterface;
use Hrvoje\PhpFramework\Response\Response;
use Hrvoje\PhpFramework\Response\ResponseInterface;

class Router
{
    private array $routes;
    private Route $defaultRoute;

    public function __construct(Route $defaultRoute = null)
    {
        $this->routes = [];
        $this->defaultRoute = $defaultRoute ?? new Route("/not-found", Method::Get, function () {
            return new Response("<h1>Not Found</h1>");
        });
    }

    public function addRoute(Route $route): Router
    {
        $this->routes[] = $route;
        return $this;
    }

    public function resolve(RequestInterface $request): ResponseInterface
    {
        return $this->tryFindRoute($request)->resolve();
    }

    private function tryFindRoute(RequestInterface $request): Route
    {
        /** @var Route $route */
        foreach ($this->routes as &$route) {
            if ($route->match($request)) {
                return $route;
            }
        }

        return $this->defaultRoute;
    }
}
