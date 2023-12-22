<?php

namespace Database\Factories;

use App\Enums\TaskStatusEnum;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'status' => TaskStatusEnum::todo,
            'priority' => fake()->randomElement(range(1, 5)),
            'title' => fake()->words(3, true),
            'description' => fake()->paragraph(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
