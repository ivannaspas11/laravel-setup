<?php

namespace App\Observers;

use App\Models\Task;
use Illuminate\Support\Facades\Log;

class TaskObserver
{
    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {
        Log::info("Створено нове завдання: ID={$task->id}, Назва='{$task->title}'.");
    }

    /**
     * Handle the Task "updated" event.
     */
    public function updated(Task $task): void
    {
        if ($task->wasChanged('status')) {
            $oldStatus = $task->getOriginal('status');
            $newStatus = $task->status;

            Log::info("Статус завдання ID={$task->id} змінено з '{$oldStatus}' на '{$newStatus}'.");
        }
    }

    /**
     * Handle the Task "deleted" event.
     */
    public function deleted(Task $task): void
    {
        Log::info("Видалено завдання: ID={$task->id}, Назва='{$task->title}'.");
    }
}