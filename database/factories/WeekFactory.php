<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Week;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Week>
 */
class WeekFactory extends Factory
{
    protected $model = Week::class;

    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'number' => fake()->numberBetween(1, 16),
            'title' => 'Semana ' . fake()->numberBetween(1, 16) . ': ' . fake()->sentence(3),
            'description' => fake()->paragraph(),
        ];
    }

    public function forCourse(Course $course): static
    {
        return $this->state(fn (array $attributes) => [
            'course_id' => $course->id,
        ]);
    }

    public function withNumber(int $number): static
    {
        return $this->state(fn (array $attributes) => [
            'number' => $number,
            'title' => "Semana {$number}: " . fake()->sentence(3),
        ]);
    }
}
