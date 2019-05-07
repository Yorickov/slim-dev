<?php

namespace Slim\Dev;

class Generator
{
    public static function generate($count)
    {
        $numbers = range(1, $count);
        shuffle($numbers);

        $faker = \Faker\Factory::create();
        $faker->seed(1);
        $users = [];
        for ($i = 0; $i < $count; $i++) {
            $users[] = [
                'id' => $numbers[$i],
                'name' => $faker->text(70),
                'body' => $faker->sentence,
                'slug' => $faker->slug
            ];
        }

        return $users;
    }
}
