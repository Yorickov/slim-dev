<?php

namespace Slim\Dev\Services;

class DataMapper
{
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function query(string $sql, array $params = [])
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (DOException $e) {
            echo "Error!: {$e->getMessage()}";
            die();
        }
    }
}
