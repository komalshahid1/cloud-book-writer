<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Book;
use App\Models\User;

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
    protected $model = Book::class;
    
    public function definition(): array
    {
        return [
             'title' => $this->faker->sentence(3),
             'author_id' => User::factory()->create(['role' => 'author'])->id, 
              
        ];
    }
}
