<?php

namespace Hrvoje\PhpFramework\Router;

use Hrvoje\PhpFramework\Request\RequestInterface;
use Hrvoje\PhpFramework\Response\ResponseInterface;

class Route
{
    private string $url;
    private Method $method;
    /**
     * @var callable
     */
    private $callback;
    private array $parameters;

    /**
     * @param callable(): mixed $callback
     */
    public function __construct(string $url, Method $method, callable $callback)
    {
        $this->url = $url;
        $this->method = $method;
        $this->callback = $callback;
        $this->parameters = $this->parseParams($url);
    }

    public function resolve(): ResponseInterface
    {
        return call_user_func_array($this->callback, $this->parameters);
    }

    public function match(RequestInterface $request): bool
    {
        if (!$this->matchMethod($request->getRequestMethod())) {
            return false;
        }

        if (count($this->parameters) === 0) {
            return $this->matchUrlLiteral($request->getRequestUrl());
        } else {
            return $this->matchParameterizedUrl($request->getRequestUrl());
        }
    }

    private function matchUrlLiteral(string $url): bool
    {
        return $this->url === $url;
    }

    private function matchParameterizedUrl(string $url): bool
    {
        $original_url_split = explode("/", $this->url);
        $url_plit = explode("/", $url);

        $original_url_length = count($original_url_split);

        if ($original_url_length !== count($url_plit)) {
            return false;
        }

        $parameters = [];
        for ($i = 0; $i < $original_url_length; $i++) {
            $original_part = $original_url_split[$i];
            $part = $url_plit[$i];

            if (strpos($original_part, "{") !== false) {
                $parameters[trim($original_part, "{}")] = $part;
                continue;
            }

            if ($original_part !== $part) {
                return false;
            }
        }

        $this->parameters = array_merge($this->parameters, $parameters);

        return true;
    }

    private function matchMethod(Method $method): bool
    {
        return $this->method === $method;
    }

    private function parseParams(string $url): array
    {
        $parameters = [];

        if (strpos($url, "{") !== false) {
            $matches = [];
            $result = preg_match_all("/{(\w+)}/u", $url, $matches);
            if (is_numeric($result) && $result > 0) {
                $parameters = array_fill_keys($matches[1], null);
            }
        }

        return $parameters;
    }

    /**
     * @param callable $callback
     */
    public static function get(string $url, callable $callback): void
    {
        Router::addRoute(new Route($url, Method::Get, $callback));
    }

    /**
     * @param callable $callback
     */
    public static function post(string $url, callable $callback): void
    {
        Router::addRoute(new Route($url, Method::Post, $callback));
    }
}
