<?php

namespace Slim\Dev\Models;

class User
{
    private $id;
    private $passwordDigest;

    public function __construct($nickname, $password)
    {
        $this->nickname = $nickname;
        $this->password = $password;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setPasswordDigest($passwordDigest)
    {
        $this->passwordDigest = $passwordDigest;
    }

    public function getPasswordDigest()
    {
        return $this->passwordDigest;
    }

    public function getNickname()
    {
        return $this->nickname;
    }

    public function getPassword()
    {
        return $this->password;
    }
    
    public function getProps()
    {
        return get_object_vars($this);
    }
}
