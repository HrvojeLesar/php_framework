<?php

namespace Hrvoje\PhpFramework\Router;

use Hrvoje\PhpFramework\Exceptions\RouteNotFoundException;
use Hrvoje\PhpFramework\Request\RequestInterface;
use Hrvoje\PhpFramework\Response\ResponseInterface;

class Router
{
    /** @var Route[] $routes */
    private static array $routes = [];

    public static function addRoute(Route $route): void
    {
        static::$routes[] = $route;
    }

    /**
     * @throws RouteNotFoundException
     */
    public static function resolve(RequestInterface $request): ResponseInterface
    {
        return static::tryFindRoute($request)->resolve($request);
    }

    /**
     * @throws RouteNotFoundException
     */
    private static function tryFindRoute(RequestInterface $request): Route
    {
        /** @var Route $route */
        foreach (static::$routes as &$route) {
            if ($route->match($request)) {
                return $route;
            }
        }

        throw new RouteNotFoundException("No route was found for requested url");
    }
}
