<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Insight;
use App\Models\Comment;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class InsightSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Create a user with random data
        $user = User::firstOrCreate([
            'email' => 'support@ekilie.com', // Using a specific email for repeated seed runs
        ], [
            'name' => "Sasi W Sasi",
            'password' => bcrypt('tachy2004'),
        ]);

        // Create some categories ensuring uniqueness
        foreach (range(1, 5) as $index) {
            $name = $faker->word(); // Generate random category name
            $category = Category::firstOrCreate([
                'name' => $name,
            ], [
                'slug' => Str::slug($name) . '-' . uniqid(), // Ensure unique slug
            ]);

            // Create insights with dynamic content
            $insight = Insight::create([
                'title' => $faker->sentence(6), // Generate random title
                'content' => '<p>' . implode('</p><p>', $faker->paragraphs(3)) . '</p>', // Generate random paragraphs
                'slug' => Str::slug($faker->sentence(3)) . '-' . uniqid(), // Generate unique slug
                'user_id' => $user->id,
                'category_id' => $category->id,
            ]);

            // Add comments to each insight with dynamic data
            Comment::create([
                'comment' => $faker->sentence(), // Generate random comment
                'user_id' => $user->id,
                'insight_id' => $insight->id,
            ]);
        }
    }
}
