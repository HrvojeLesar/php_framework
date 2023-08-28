<?php

namespace Hrvoje\PhpFramework\Model;

use DateTime;
use Hrvoje\PhpFramework\Database\Connection;

trait SoftDelete
{
    protected static $deletedAtColumn = "deleted_at";

    protected function getDeletedAtColumn(): string
    {
        return static::$deletedAtColumn;
    }

    protected function setDeletedAtColumn(): void
    {
        $this->{$this->getDeletedAtColumn()} = date(DateTime::ATOM);
    }

    public function softDelete(): void
    {
        var_dump($this->columns);
        $this->setDeletedAtColumn();
        $connection = Connection::getInstance();
        $connection->update(static::getTableName(), $this->columns, [[static::$primaryKeyColumn, '=', $this->columns[static::$primaryKeyColumn]]]);
    }
}
