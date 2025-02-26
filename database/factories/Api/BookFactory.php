<?php

namespace Database\Factories\Api;

use App\Models\Api\Author;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'=>fake()->name(),
            'pages'=>fake()->randomNumber(5,false),
            'author_id'=> Author::factory()
        ];
    }
}
