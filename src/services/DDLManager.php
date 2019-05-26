<?php

namespace Slim\Dev\Services;

class DDLManager implements DDLManagerInterface
{
    private $pdo;

    public function __construct($dsn, $user = null, $pass = null, $options = [])
    {
        $this->pdo = new \PDO($dsn, $user, $pass, $options);
    }

    public function createTable($table, array $params)
    {
        $fieldParts = array_map(function ($key, $value) {
            return "{$key} {$value}";
        }, array_keys($params), $params);
        $fieldsDescription = implode(", ", $fieldParts);
        $sql = sprintf("CREATE TABLE IF NOT EXISTS %s (%s)", $table, $fieldsDescription);
        return $this->pdo->exec($sql);
    }

    public function getConnection()
    {
        return $this->pdo;
    }
}
