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

    /**
     * @return static|null
     * @throws PDOException
     */
    public static function find(int $primaryKey): static|null
    {
        $item = Connection::getInstance()->select(sprintf("SELECT * FROM %s WHERE %s = %s", static::getTableName(), static::$primaryKeyColumn, $primaryKey))->fetchAssoc();
        if ($item === false) {
            return null;
        }
        $model = new static();
        $model->setColumns($item);
        return $model;
    }

    /**
     * @param array $data
     */
    protected function setColumns(array $data): void
    {
        $columns = static::getTableColumns();
        $this->{static::$primaryKeyColumn} = $data[static::$primaryKeyColumn];
        foreach($columns as &$column) {
            $this->$column = $data[$column];
        }
    }

    public function toArray(): array
    {
        $id = $this->{static::$primaryKeyColumn};
        $modelArray = isset($id) ? [static::$primaryKeyColumn => $id] : [];
        $columns = static::getTableColumns();
        foreach($columns as &$column) {
            if (is_null($this->$column)) {
                continue;
            }
            $modelArray[$column] = $this->$column;
        }
        return $modelArray;
    }
}
