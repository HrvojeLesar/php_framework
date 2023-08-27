<?php

namespace Hrvoje\PhpFramework\Response;

abstract class BaseResponse
{
    protected mixed $data;

    public function __construct(mixed $data)
    {
        $this->data = $data;
    }
}
