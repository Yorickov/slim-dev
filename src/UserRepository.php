<?php

namespace Slim\Dev;

class UserRepository
{
    public function __construct()
    {
        if (!array_key_exists('users', $_SESSION)) {
            $_SESSION['users'] = [];
        }
    }

    public function all()
    {
        return array_values($_SESSION['users']);
    }

    public function find(string $id)
    {
        return $_SESSION['users'][$id];
    }

    public function destroy(string $id)
    {
        unset($_SESSION['users'][$id]);
    }

    public function save(array $item)
    {
        if (empty($item['nickname']) || empty($item['password'])) {
            $json = json_encode($item);
            throw new \Exception("Wrong data: {$json}");
        }
        if (!isset($item['id'])) {
            $item['id'] = uniqid();
        }
        $item['passwordDigest'] = hash('sha256', $item['password']);
        unset($item['password']);
        
        $_SESSION['users'][$item['id']] = $item;
        return $item['id'];
    }
}
