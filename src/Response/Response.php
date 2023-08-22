<?php

namespace Hrvoje\PhpFramework\Response;

class Response extends BaseResponse implements ResponseInterface
{
    public function send(): string
    {
        return $this->data;
    }
}
