<?php

use Hrvoje\PhpFramework\Request\Request;
use Hrvoje\PhpFramework\Router\Router;

require __DIR__.'/vendor/autoload.php';

global $ROUTER;
$ROUTER = new Router();

include "routes.php";

$request = new Request();
echo $ROUTER->resolve($request)->send();
