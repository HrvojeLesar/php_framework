<?php

namespace Hrvoje\PhpFramework\Model;

use Hrvoje\PhpFramework\Database\Connection;
use InvalidArgumentException;
use PDOException;

abstract class Model
{
    protected static string $primaryKeyColumn = "id";

    abstract protected static function getTableName(): string;

    abstract protected function setId(mixed $id): void;

    /**
     * @return int[]|string[]
     */
    protected static function getTableColumns(): array
    {
        $columns = get_class_vars(get_called_class());
        unset($columns["primaryKeyColumn"]);
        return array_keys($columns);
    }

    protected function getModelData(): array
    {
        $columns = static::getTableColumns();
        $data = [];
        foreach($columns as $column) {
            if (is_null($this->$column) || $column === static::$primaryKeyColumn) {
                continue;
            }
            $data[$column] = $this->$column;
        }
        return $data;
    }


    /**
     * @return void
     * @throws PDOException
     * @throws InvalidArgumentException
     */
    public function save(): void
    {
        $connection = Connection::getInstance();
        $connection->insert(static::getTableName(), $this->getModelData());
        $id = $connection->getDatabaseConnection()->lastInsertId(static::getTableName());
        $this->setId($id);
    }
    /**
     * @return void
     * @throws PDOException
     */
    public function update(): void
    {
        $id = $this->{static::$primaryKeyColumn};
        if (isset($id)) {
            $connection = Connection::getInstance();
            $connection->update(static::getTableName(), $this->getModelData(), [[static::$primaryKeyColumn, "=", $id]]);
        }
    }
}
