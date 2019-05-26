<?php

namespace Slim\Dev\Models;

class Post
{
    private $id;

    public function __construct($name, $body)
    {
        $this->name = $name;
        $this->body = $body;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
    
    public function getProps()
    {
        return get_object_vars($this);
    }
}
