<?php

namespace Hrvoje\PhpFramework\Request;

use Hrvoje\PhpFramework\Router\Method;

class Request implements RequestInterface
{
    public function __construct()
    {
    }

    public function getRequestParams(): array
    {
        return match ($this->getRequestMethod()) {
            Method::Get => $_GET,
            Method::Post => $_POST,
        };
    }

    public function getRequestMethod(): Method
    {
        return Method::fromString($_SERVER["REQUEST_METHOD"] ?? "GET");
    }

    public function getRequestUrl(): string
    {
        return $_SERVER["PATH_INFO"] ?? "/";
    }
}
