<?php

namespace Hrvoje\PhpFramework\Model;

use DateTime;

trait HasTimestamps
{
    protected static $createdAtColumn = "created_at";
    protected static $updatedAtColumn = "updated_at";

    protected function getCreatedAtColumn(): string
    {
        return static::$createdAtColumn;
    }

    protected function getUpdatedAtColumn(): string
    {
        return static::$updatedAtColumn;
    }

    protected function setCreatedAt(): void
    {
        $this->{$this->getCreatedAtColumn()} = date(DateTime::ATOM);
    }

    protected function setUpdatedAt(): void
    {
        $this->{$this->getUpdatedAtColumn()} = date(DateTime::ATOM);
    }
}
