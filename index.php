<?php

use Hrvoje\PhpFramework\Request\Request;
use Hrvoje\PhpFramework\Router\RouterSingleton;

require __DIR__.'/vendor/autoload.php';

include "routes.php";

$request = new Request();
echo RouterSingleton::router()->resolve($request)->send();
