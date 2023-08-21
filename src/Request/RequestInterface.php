<?php

namespace Hrvoje\PhpFramework\Request;

use Hrvoje\PhpFramework\Router\Method;

interface RequestInterface
{
    public function getRequestMethod(): Method;
    /**
     * @return array
     */
    public function getRequestParams(): array;
    public function getRequestUrl(): string;
}
