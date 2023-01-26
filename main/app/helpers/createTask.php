<?php

use App\Models\Person;
use App\Models\Task;
use App\Models\TaskTimeline;

if (!function_exists('createTask')) {
    function createTask($data)
    {
        $allocator_person = Person::where('id', $data['allocator_person_id'])->fullName()->first();
        $task = Task::create($data);
        TaskTimeline::create([
            'icon' => 'fas fa-tasks',
            'title' => 'Nueva tarea',
            'description' => $allocator_person->full_names . ' creó la tarea.',
            'task_id' => $task->id,
            'person_id' => $allocator_person->id,

        ]);
        return 'Creado con éxito';
    }
}
