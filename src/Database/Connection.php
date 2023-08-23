<?php

namespace Hrvoje\PhpFramework\Database;

use PDO;
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

    protected function getDatabaseConnection(): PDO
    {
        if (is_null(static::$databaseConnection)) {
            static::$databaseConnection = static::newConnection();
        }

        return static::$databaseConnection;
    }

    protected static function newConnection(): PDO
    {
        $dbname = getenv("PHPFRAMEWORK_DBNAME") ?: "testdb";
        $user = getenv("PHPFRAMEWORK_DBUSER") ?: "testuser";
        $pass = getenv("PHPFRAMEWORK_DBPASS") ?: "testpass";

        // TODO: Handle expcetion...
        return new PDO("mysql:host=localhost;dbname=".$dbname, $user, $pass);
    }

    /**
     * @param array $params
     */
    public function select(string $query, array $params): Connection
    {
        $this->statement = $this->getDatabaseConnection()->prepare($query);
        $this->bindSelectParams($params);
        $this->statement->execute();

        return $this;
    }

    /**
     * @param array $params
     */
    protected function bindSelectParams(array $params): void
    {
        if ($this->statement instanceof PDOStatement) {
            $is_indexed_array = array_is_list($params);
            foreach($params as $paramKey => $paramValue) {
                if ($is_indexed_array) {
                    $paramKey = (int)$paramKey + 1;
                }
                $this->statement->bindValue($paramKey, $paramValue);
            }
        }
    }

    public function fetchAssoc(): mixed
    {
        if ($this->statement instanceof PDOStatement) {
            return $this->statement->fetch();
        } else {
            return null;
        }
    }

    public function fetchAssocAll(): mixed
    {
        if ($this->statement instanceof PDOStatement) {
            return $this->statement->fetchAll();
        } else {
            return null;
        }
    }

    /**
     * @return void
     * @param array $data
     */
    public function insert(string $table, array $data): void
    {
        $paramNames = $this->extractParameterKeyNames($data);
        $elementCount = $this->countNumberOfElementsToInsert($data);

        if (count($paramNames) === 0 || $elementCount === 0) {
            return;
        }

        $query = $this->constructInsertQuery($table, $data, $paramNames, $elementCount);
        $this->statement = $this->getDatabaseConnection()->prepare($query);

        $this->bindInsertParams($paramNames, $data, $elementCount);
        $this->statement->execute();
    }

    /**
     * @param array $data
     */
    protected function isSingleElementInsertion(array $data): bool
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
        if ($this->isSingleElementInsertion($data)) {
            return array_keys($data);
        } else {
            return array_keys(isset($data[0]) ? $data[0] : []);
        }
    }
    /**
     * @param array $data
     * @param array $paramNames
     */
    protected function constructInsertQuery(string $table, array $data, array $paramNames, int $elementCount): string
    {
        $innerParamValues = implode(",", array_fill(0, count($paramNames), "?"));
        $paramValues = implode(",", array_fill(0, $elementCount, sprintf("(%s)", $innerParamValues)));
        return sprintf("INSERT INTO %s(%s) VALUES %s", $table, implode(",", $paramNames), $paramValues);
    }
    /**
     * @param array $data
     */
    protected function countNumberOfElementsToInsert(array $data): int
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
    protected function bindInsertParams(array $paramNames, array $data, int $elementCount): void
    {
        if ($this->statement instanceof PDOStatement) {
            $isSingleElement = $this->isSingleElementInsertion($data);
            $paramIndex = 1;
            if ($isSingleElement) {
                foreach($paramNames as &$param) {
                    $this->statement->bindValue($paramIndex++, $data[$param]);
                }
            } else {
                foreach($data as &$element) {
                    foreach($paramNames as &$param) {
                        $this->statement->bindValue($paramIndex++, $element[$param]);
                    }
                }
            }
        }
    }
}
