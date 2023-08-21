<?php

namespace Hrvoje\PhpFramework\Router;

enum Method
{
    case Get;
    case Post;

    public function getMethodName(): string
    {
        return match ($this) {
            Method::Get => "GET",
            Method::Post => "POST"
        };
    }

    public static function fromString(string $method): Method
    {
        return match ($method) {
            "GET" => Method::Get,
            "POST" => Method::Post,
            default => Method::Get,
        };
    }
}
