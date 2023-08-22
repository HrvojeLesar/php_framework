<?php

use Hrvoje\PhpFramework\Response\Response;
use Hrvoje\PhpFramework\Response\ResponseType;
use Hrvoje\PhpFramework\Router\Route;

Route::get("/", function () {
    return new Response("<h1>Hello World!</h1>", ResponseType::Plaintext);
});

Route::post("/", function () {
    return new Response(["some" => "json", "response" => "data"], ResponseType::Json);
});

Route::get("/route1", function () {
    return new Response("Hello World again!", ResponseType::Plaintext);
});
