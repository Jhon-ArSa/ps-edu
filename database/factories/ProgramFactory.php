<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProgramFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'               => fake()->words(3, true) . ' en ' . fake()->word(),
            'code'               => strtoupper(fake()->lexify('???')) . '-' . fake()->numerify('###'),
            'degree_type'        => fake()->randomElement(['maestria', 'doctorado', 'segunda_especialidad', 'diplomado']),
            'description'        => fake()->sentence(),
            'duration_semesters' => fake()->randomElement([2, 4, 6]),
            'has_propedeutic'    => false,
            'total_credits'      => fake()->numberBetween(30, 120),
            'status'             => 'active',
            'coordinator_id'     => null,
        ];
    }
}
