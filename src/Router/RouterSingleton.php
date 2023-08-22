<?php

namespace Hrvoje\PhpFramework\Router;

class RouterSingleton
{
    protected static ?Router $routerInstance = null;

    public static function router(): Router
    {
        if (is_null(static::$routerInstance)) {
            static::$routerInstance = new Router();
        }
        return static::$routerInstance;
    }
}
