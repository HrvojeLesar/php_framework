<?php

namespace Hrvoje\PhpFramework\Response;

class Response implements ResponseInterface
{
    // private array $headers;
    private ResponseType $responseType;
    private mixed $data;

    public function __construct(mixed $data, ResponseType $responseType = ResponseType::Plaintext)
    {
        $this->data = $data;
        $this->responseType = $responseType;
    }

    public function setResponseType(ResponseType $responseType): Response
    {
        $this->responseType = $responseType;
        return $this;
    }

    public function send(): string
    {
        return match ($this->responseType) {
            ResponseType::Json => json_encode($this->data),
            ResponseType::EscapedHtml => htmlspecialchars($this->data),
            ResponseType::Plaintext => $this->data,
        };
    }
}
