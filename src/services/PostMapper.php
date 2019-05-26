<?php

namespace Slim\Dev\Services;

class PostMapper extends DataMapper
{
    public function findById(string $id)
    {
        $sql = "SELECT * FROM posts WHERE id = ?";
        $preparedId = [$id];
        $stmt = $this->query($sql, $preparedId);
        return $stmt->fetch();
    }

    public function save(\Slim\Dev\Models\Post $post)
    {
        if (null === $post->getId()) {
            $id = uniqid();
            $post->setId($id);
            $props = $post->getProps();
            $values = array_values($props);

            $preparedFields = implode(', ', array_keys($props));
            $preparedValues = str_repeat('?, ', count($values) - 1) . '?';
            $sql = "INSERT INTO posts ($preparedFields) VALUES ($preparedValues)";
        } else {
            $props = array_slice($post->getProps(), 1);
            $values = array_merge(array_values($props), [$post->getId()]);

            $preparedArrayFields = array_map(function ($field) {
                return $field . " = ?";
            }, array_keys($props));
            $preparedFields = implode(', ', $preparedArrayFields);
            $sql = "UPDATE posts SET $preparedFields WHERE id = ?";
        }

        // $params = [
        //     $post->getId(),
        //     $post->getName(),
        //     $post->getBody()
        // ];
        
        $this->query($sql, $values);
        return $post->getId();
    }

    public function all()
    {
        $sql = "SELECT * FROM posts";
        $stmt = $this->query($sql);
        return $stmt->fetchAll();
    }

    public function validate(array $post)
    {
        $errors = [];
        if ($post['name'] == '') {
            $errors['name'] = "Can not be blank";
        }

        if (empty($post['body'])) {
            $errors['body'] = "Can not be blank";
        }

        return $errors;
    }

    public function destroy(string $id)
    {
        $sql = "DELETE FROM posts WHERE id = ?";
        $preparedId = [$id];
        $this->query($sql, $preparedId);
    }
}
