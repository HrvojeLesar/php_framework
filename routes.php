<?php

use Hrvoje\PhpFramework\Response\Response;
use Hrvoje\PhpFramework\Response\ResponseType;
use Hrvoje\PhpFramework\Router\Method;
use Hrvoje\PhpFramework\Router\Route;

$ROUTER->addRoute(new Route("/", Method::Get, function () {
    return new Response("<h1>Hello World!</h1>", ResponseType::Plaintext);
}));

$ROUTER->addRoute(new Route("/", Method::Post, function () {
    return new Response(["some" => "json", "response" => "data"], ResponseType::Json);
}));

$ROUTER->addRoute(new Route("/route1", Method::Get, function () {
    return new Response("Hello World again!", ResponseType::Plaintext);
}));
