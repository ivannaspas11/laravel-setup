<?php

namespace App\Listeners;

use App\Events\TaskCreated;
use Illuminate\Support\Facades\Log;

class NotifyAssignee
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TaskCreated $event): void
    {
        $task = $event->task;

        if ($task->assigned_to) {
            Log::info("Сповіщення: завдання ID={$task->id} ('{$task->title}') призначено виконавцю з ID={$task->assigned_to}.");
        }
    }
}