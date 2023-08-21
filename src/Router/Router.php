<?php

namespace Hrvoje\PhpFramework\Router;

use Hrvoje\PhpFramework\Request\RequestInterface;
use Hrvoje\PhpFramework\Response\Response;
use Hrvoje\PhpFramework\Response\ResponseInterface;
use Hrvoje\PhpFramework\Response\ResponseType;

class Router
{
    private array $routes;
    private Route $default_route;

    public function __construct()
    {
        $this->routes = [];
        $this->default_route = new Route("/not-found", Method::Get, function () {
            return new Response("<h1>Not Found</h1>", ResponseType::Plaintext);
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
            if ($route->match($request->getRequestUrl(), $request->getRequestMethod())) {
                return $route;
            }
        }

        return $this->default_route;
    }
}
