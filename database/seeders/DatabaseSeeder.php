<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::factory(3)->create();

        $users->each(function (User $user) use ($users) {
            $projects = Project::factory(2)->create([
                'user_id' => $user->id,
            ]);

            $projects->each(function (Project $project) use ($user, $users) {
                $tasks = Task::factory(5)->create([
                    'project_id' => $project->id,
                    'author_id' => $user->id,
                    'assigned_to' => fn () => $users->random()->id,
                ]);

                $tasks->each(function (Task $task) use ($users) {
                    Comment::factory(rand(2, 3))->create([
                        'task_id' => $task->id,
                        'user_id' => fn () => $users->random()->id,
                    ]);
                });
            });
        });
    }
}
