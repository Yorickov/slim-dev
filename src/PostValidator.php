<?php

namespace Slim\Dev;

class PostValidator
{
    public function validate(array $course)
    {
        $errors = [];
        if ($course['name'] == '') {
            $errors['name'] = "Can not be blank";
        }

        if (empty($course['body'])) {
            $errors['body'] = "Can not be blank";
        }

        return $errors;
    }
}
