<?php

use Hrvoje\PhpFramework\Request\Request;
use Hrvoje\PhpFramework\Router\Router;

require __DIR__.'/vendor/autoload.php';

require_once "routes.php";

$request = new Request();
echo Router::resolve($request)->send();
