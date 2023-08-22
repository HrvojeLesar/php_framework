<?php

namespace Hrvoje\PhpFramework\Router;

use Closure;
use Hrvoje\PhpFramework\Response\ResponseInterface;

class Route
{
    private string $url;
    private Method $method;
    public \Closure $callback;

    /**
     * @param Closure(): ResponseInterface $callback
     */
    public function __construct(string $url, Method $method, \Closure $callback)
    {
        $this->url = $url;
        $this->method = $method;
        $this->callback = $callback;
    }

    public function resolve(): ResponseInterface
    {
        return call_user_func($this->callback);
    }

    public function match(string $url, Method $method): bool
    {
        return $this->urlMatches($url) && $this->methodMatches($method);
    }

    private function urlMatches(string $url): bool
    {
        return $this->url == $url;
    }

    private function methodMatches(Method $method): bool
    {
        return $this->method === $method;
    }

    /**
     * @param Closure(): ResponseInterface $callback
     */
    public static function get(string $url, \Closure $callback): void
    {
        RouterSingleton::router()->addRoute(new Route($url, Method::Get, $callback));
    }

    /**
     * @param Closure(): ResponseInterface $callback
     */
    public static function post(string $url, \Closure $callback): void
    {
        RouterSingleton::router()->addRoute(new Route($url, Method::Post, $callback));
    }
}
