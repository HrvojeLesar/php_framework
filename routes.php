<?php

use Hrvoje\PhpFramework\Controller\IndexController;
use Hrvoje\PhpFramework\Response\Response;
use Hrvoje\PhpFramework\Router\Route;

Route::get("/", [IndexController::class, "indexAction"]);

Route::post("/", [IndexController::class, "indexJsonAction"]);

Route::get("/route1", function () {
    return new Response("Hello World again!");
});

Route::get("/route/{with}/{params}", function (int $with, string $params) {
    return new Response($with . $params);
});
