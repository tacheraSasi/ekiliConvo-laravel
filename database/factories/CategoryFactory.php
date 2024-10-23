<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition()
    {
        $name = $this->faker->word; // Generate a random word for the category name
        return [
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name), // Generate a slug from the name
        ];
    }
}
