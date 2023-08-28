<?php

namespace Hrvoje\PhpFramework\Database;

use Hrvoje\PhpFramework\Exceptions\EnvNotSetException;
use InvalidArgumentException;
use PDO;
use PDOException;
use PDOStatement;

class Connection
{
    private static ?Connection $connection = null;
    private static ?PDO $databaseConnection = null;

    protected PDOStatement|false|null $statement;

    private function __construct()
    {
        $this->statement = null;
    }

    public static function getInstance(): Connection
    {
        if (is_null(static::$connection)) {
            static::$connection = new Connection();
        }
        return static::$connection;
    }

    public function getDatabaseConnection(): PDO
    {
        if (is_null(static::$databaseConnection)) {
            static::$databaseConnection = static::newConnection();
        }

        return static::$databaseConnection;
    }

    /**
     * @throws PDOException
     */
    protected static function newConnection(): PDO
    {
        $dbname = getenv("PHPFRAMEWORK_DBNAME") ?? throw new EnvNotSetException("PHPFRAMEWORK_DBNAME env variable must be set!");
        $user = getenv("PHPFRAMEWORK_DBUSER") ?? throw new EnvNotSetException("PHPFRAMEWORK_DBUSER env variable must be set!");
        $pass = getenv("PHPFRAMEWORK_DBPASS") ?? throw new EnvNotSetException("PHPFRAMEWORK_DBPASS env variable must be set!");

        return new PDO("mysql:host=localhost;dbname=".$dbname, $user, $pass);
    }

    /**
     * @param array $params
     * @throws PDOException
     */
    public function select(string $query, array $params = []): Connection
    {
        $this->statement = $this->getDatabaseConnection()->prepare($query);
        if (count($params) > 0) {
            $this->bindSelectParams($params);
        }
        $this->statement->execute();

        return $this;
    }

    /**
     * @param array $params
     */
    protected function bindSelectParams(array $params): void
    {
        if ($this->statement instanceof PDOStatement) {
            $isIndexedArray = array_is_list($params);
            foreach($params as $paramKey => $paramValue) {
                $this->statement->bindValue($isIndexedArray ? (int)$paramKey + 1 : $paramKey, $paramValue);
            }
        }
    }

    public function fetchAssoc(): mixed
    {
        if ($this->statement instanceof PDOStatement) {
            return $this->statement->fetch(PDO::FETCH_ASSOC);
        } else {
            return null;
        }
    }

    public function fetchAssocAll(): mixed
    {
        if ($this->statement instanceof PDOStatement) {
            return $this->statement->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return null;
        }
    }

    /**
     * @return void
     * @param array $data
     * @throws InvalidArgumentException
     * @throws PDOException
     */
    public function insert(string $table, array $data): void
    {
        $paramNames = $this->extractParameterKeyNames($data);
        $itemCount = $this->countItems($data);

        if (count($paramNames) === 0 || $itemCount === 0) {
            throw new InvalidArgumentException("Data must contain at least one item");
        }

        $query = $this->constructInsertQuery($table, $data, $paramNames, $itemCount);
        $this->statement = $this->getDatabaseConnection()->prepare($query);

        $this->bindInsertParams($paramNames, $data, $itemCount);
        $this->statement->execute();
    }

    /**
     * @param array $data
     */
    protected function isSingleItemInsertion(array $data): bool
    {
        if (array_is_list($data) && count($data) > 0) {
            return false;
        }
        return true;
    }
    /**
     * @return int[]|string[]
     * @param array $data
     */
    protected function extractParameterKeyNames(array $data): array
    {
        if ($this->isSingleItemInsertion($data)) {
            return array_keys($data);
        } else {
            return array_keys(isset($data[0]) ? $data[0] : []);
        }
    }
    /**
     * @param array $data
     * @param array $paramNames
     */
    protected function constructInsertQuery(string $table, array $data, array $paramNames, int $itemCount): string
    {
        $innerParamValues = implode(",", array_fill(0, count($paramNames), "?"));
        $paramValues = implode(",", array_fill(0, $itemCount, sprintf("(%s)", $innerParamValues)));
        return sprintf("INSERT INTO %s(%s) VALUES %s", $table, implode(",", $paramNames), $paramValues);
    }
    /**
     * @param array $data
     */
    protected function countItems(array $data): int
    {
        $count = count($data);
        if (array_is_list($data)) {
            return $count;
        } elseif ($count > 0) {
            return 1;
        } else {
            return 0;
        }
    }
    /**
     * @return void
     * @param array $paramNames
     * @param array $data
     */
    protected function bindInsertParams(array $paramNames, array $data, int $itemCount): void
    {
        if ($this->statement instanceof PDOStatement) {
            $isSingleItem = $this->isSingleItemInsertion($data);
            $paramIndex = 1;
            if ($isSingleItem) {
                foreach($paramNames as &$param) {
                    $this->statement->bindValue($paramIndex++, $data[$param]);
                }
            } else {
                foreach($data as &$item) {
                    foreach($paramNames as &$param) {
                        $this->statement->bindValue($paramIndex++, $item[$param]);
                    }
                }
            }
        }
    }

    /**
     * @return void
     * @param array $data
     * @param array|null $conditions
     * @throws PDOException
     */
    public function update(string $table, array $data, array|null $conditions): void
    {
        $set = implode(", ", array_map(function ($key) {
            return sprintf("%s=?", $key);
        }, array_keys($data)));

        $params = array_values($data);

        if (isset($conditions)) {
            $where = implode(" AND ", array_map(function ($condition) use (&$params) {
                if (is_array($condition) && count($condition) === 3) {
                    $params[] = $condition[2];
                    return sprintf("%s%s?", $condition[0], $condition[1]);
                }
                return "";
            }, $conditions));
            $query = sprintf("UPDATE %s SET %s WHERE %s", $table, $set, $where);
        } else {
            $query = sprintf("UPDATE %s SET %s", $table, $set);
        }

        $this->statement = $this->getDatabaseConnection()->prepare($query);
        $this->statement->execute($params);
    }

    /**
     * @return void
     * @param array $data
     * @param array|null $conditions
     * @throws PDOException
     */
    public function delete(string $table, array|null $conditions): void
    {
        $params = [];
        if (isset($conditions)) {
            $where = implode(" AND ", array_map(function ($condition) use (&$params) {
                if (is_array($condition) && count($condition) === 3) {
                    $params[] = $condition[2];
                    return sprintf("%s%s?", $condition[0], $condition[1]);
                }
                return "";
            }, $conditions));
            $query = sprintf("DELETE FROM %s WHERE %s", $table, $where);
        } else {
            $query = sprintf("DELETE FROM %s", $table);
        }

        $this->statement = $this->getDatabaseConnection()->prepare($query);
        $this->statement->execute($params);
    }
}
