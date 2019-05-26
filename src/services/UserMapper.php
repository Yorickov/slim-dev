<?php

namespace Slim\Dev\Services;

class UserMapper extends DataMapper
{
    public function findById(string $id)
    {
        $sql = "SELECT * FROM users WHERE id = ?";
        $preparedId = [$id];
        $stmt = $this->query($sql, $preparedId);
        return $stmt->fetch();
    }

    public function save(\Slim\Dev\Models\User $user)
    {
        $id = uniqid();
        $user->setId($id);
        $passwordDigest = hash('sha256', $user->getPassword());
        $user->setPasswordDigest($passwordDigest);
        
        $props = array_slice($user->getProps(), 0, -1);
        $values = array_values($props);
        $preparedFields = implode(', ', array_keys($props));
        $preparedValues = str_repeat('?, ', count($values) - 1) . '?';
        $sql = "INSERT INTO users ($preparedFields) VALUES ($preparedValues)";

        $this->query($sql, $values);
        return $user->getId();
    }

    public function all()
    {
        $sql = "SELECT * FROM users";
        $stmt = $this->query($sql);
        return $stmt->fetchAll();
    }

    public function validate(array $user)
    {
        $errors = [];
        if ($user['nickname'] == '') {
            $errors['nickname'] = "Can not be blank";
        }

        if (empty($user['password'])) {
            $errors['password'] = "Can not be blank";
        }

        return $errors;
    }

    public function destroy(string $id)
    {
        $sql = "DELETE FROM users WHERE id = ?";
        $preparedId = [$id];
        $this->query($sql, $preparedId);
    }
}
