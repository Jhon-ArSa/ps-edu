<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'code' => strtoupper(fake()->unique()->bothify('???-###')),
            'description' => fake()->paragraph(),
            'teacher_id' => User::factory()->create(['role' => 'docente'])->id,
            'program' => fake()->randomElement(['Maestría en Educación', 'Doctorado en Educación']),
            'cycle' => fake()->randomElement(['Primer ciclo', 'Segundo ciclo', 'Tercer ciclo']),
            'year' => fake()->year(),
            'semester' => fake()->randomElement(['I', 'II']),
            'status' => 'active',
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    public function forTeacher(User $teacher): static
    {
        return $this->state(fn (array $attributes) => [
            'teacher_id' => $teacher->id,
        ]);
    }
}
