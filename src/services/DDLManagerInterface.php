<?php

namespace Slim\Dev\Services;

interface DDLManagerInterface
{
    public function __construct($dsn, $user = null, $pass = null, $options = []);

    public function createTable($table, array $params);
}
