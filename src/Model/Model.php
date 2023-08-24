<?php

namespace Hrvoje\PhpFramework\Model;

use Hrvoje\PhpFramework\Database\Connection;
use InvalidArgumentException;
use PDOException;

abstract class Model
{
    protected static string $primaryKeyColumn = "id";

    abstract protected static function getTableName(): string;

    private array $columns = [];

    abstract public function __construct();

    /**
     * @return void
     * @throws PDOException
     * @throws InvalidArgumentException
     */
    public function save(): void
    {
        $connection = Connection::getInstance();
        $connection->insert(static::getTableName(), $this->columns);
        $id = $connection->getDatabaseConnection()->lastInsertId(static::getTableName());
        $this->columns[static::$primaryKeyColumn] = $id;
    }
    /**
     * @return void
     * @throws PDOException
     */
    public function update(): void
    {
        $id = $this->columns[static::$primaryKeyColumn];
        if (isset($id)) {
            $connection = Connection::getInstance();
            $connection->update(static::getTableName(), $this->columns, [[static::$primaryKeyColumn, "=", $id]]);
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
        $model->columns = $item;
        return $model;
    }

    public function toArray(): array
    {
        return $this->columns;
    }

    /**
     * @param mixed $name
     * @param mixed $value
     */
    public function __set($name, $value): void
    {
        $this->columns[$name] = $value;
    }

    /**
     * @param mixed $name
     */
    public function __get($name): mixed
    {
        return $this->columns[$name];
    }
}
