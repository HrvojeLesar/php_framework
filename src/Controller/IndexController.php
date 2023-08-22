<?php

namespace Hrvoje\PhpFramework\Controller;

use Hrvoje\PhpFramework\Response\JsonResponse;
use Hrvoje\PhpFramework\Response\Response;

class IndexController
{
    public static function indexAction(): Response
    {
        return new Response("<h1>Hello World!</h1>");
    }

    public static function indexJsonAction(): JsonResponse
    {
        return new JsonResponse(["some" => "json", "response" => "data"]);
    }
}
