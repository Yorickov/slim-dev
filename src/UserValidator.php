<?php

namespace Slim\Dev;

class UserValidator
{
    public function validate(array $course)
    {
        $errors = [];
        if ($course['nickname'] == '') {
            $errors['nickname'] = "Can not be blank";
        }

        if (empty($course['password'])) {
            $errors['password'] = "Can not be blank";
        }

        return $errors;
    }
}
