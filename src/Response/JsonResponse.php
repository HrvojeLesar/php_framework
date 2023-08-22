<?php

namespace Hrvoje\PhpFramework\Response;

class JsonResponse extends BaseResponse implements ResponseInterface
{
    public function send(): string
    {
        return json_encode($this->data);
    }
}
