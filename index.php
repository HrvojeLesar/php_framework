<?php

use Hrvoje\PhpFramework\Router\Router;

require __DIR__.'/vendor/autoload.php';

global $ROUTER; 
$ROUTER = new Router();

include "routes.php";

$ROUTER->resolve();
