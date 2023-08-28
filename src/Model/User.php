<?php

namespace Hrvoje\PhpFramework\Model;

class User extends Model
{
    use HasTimestamps;
    use SoftDelete;

    public function __construct()
    {
    }

    protected static function getTableName(): string
    {
        return "users";
    }
}
