<?php

use Hrvoje\PhpFramework\Router\Method;
use Hrvoje\PhpFramework\Router\Route;

$ROUTER->addRoute(new Route("/", Method::Get, function () {
    echo "<h1>Hello World!</h1>";
}));

$ROUTER->addRoute(new Route("/", Method::Post, function () {
    echo "Hello World! Post!";
}));

$ROUTER->addRoute(new Route("/route1", Method::Get, function () {
    echo "<h1>Hello World again!</h1>";
}));
