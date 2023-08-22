<?php

namespace Hrvoje\PhpFramework\Response;

class BaseResponse
{
    protected mixed $data;

    public function __construct(mixed $data)
    {
        $this->data = $data;
    }
}
