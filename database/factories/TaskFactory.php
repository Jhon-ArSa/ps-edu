<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\Week;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'week_id' => Week::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'instructions' => fake()->paragraphs(3, true),
            'due_date' => fake()->dateTimeBetween('now', '+2 weeks'),
            'max_score' => 20.0,
            'status' => 'active',
        ];
    }

    public function forWeek(Week $week): static
    {
        return $this->state(fn (array $attributes) => [
            'week_id' => $week->id,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'due_date' => fake()->dateTimeBetween('-2 weeks', '-1 day'),
        ]);
    }

    public function withFile(): static
    {
        return $this->state(fn (array $attributes) => [
            'file_path' => 'tasks/test-file.pdf',
        ]);
    }
}
