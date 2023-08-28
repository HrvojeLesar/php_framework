<?php

use Hrvoje\PhpFramework\Controller\IndexController;
use Hrvoje\PhpFramework\Controller\UserController;
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

Route::get("/user/insertUser", [UserController::class, "insertUserView"]);
Route::post("/user/insertUser", [UserController::class, "handleInsertion"]);
Route::get("/user/users", [UserController::class, "users"]);
Route::get("/user/updateUser/{userId}", [UserController::class, "updateUserView"]);
Route::post("/user/updateUser/{userId}", [UserController::class, "updateUser"]);
