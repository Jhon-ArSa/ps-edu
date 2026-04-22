<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnnouncementFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title'        => fake()->sentence(4),
            'content'      => fake()->paragraphs(2, true),
            'author_id'    => User::factory()->admin(),
            'target_role'  => fake()->randomElement(['all', 'docente', 'alumno', 'admin']),
            'published_at' => now()->subHour(),
            'image_path'   => null,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => ['published_at' => null]);
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => ['published_at' => now()->subMinute()]);
    }
}
