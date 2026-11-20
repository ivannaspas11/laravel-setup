<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
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
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['new', 'in_progress', 'done']),
            'project_id' => Project::factory(),
            'author_id' => User::factory(),
            'assigned_to' => fake()->boolean(80) ? User::factory() : null,
            'due_date' => fake()->dateTimeBetween('now', '+1 month'),
        ];
    }
}
