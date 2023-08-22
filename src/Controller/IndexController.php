<?php

namespace Hrvoje\PhpFramework\Controller;

use Hrvoje\PhpFramework\Response\JsonResponse;
use Hrvoje\PhpFramework\Response\Response;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class IndexController
{
    public static function indexAction(): Response
    {
        $loader = new FilesystemLoader(getcwd()."/templates");
        $twig = new Environment($loader);

        return new Response($twig->render("index.html", ["name" => "Template"]));
    }

    public static function indexJsonAction(): JsonResponse
    {
        return new JsonResponse(["some" => "json", "response" => "data"]);
    }
}
