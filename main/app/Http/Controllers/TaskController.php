<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\BusinessTask;
use App\Models\Person;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\TaskFile;
use App\Models\TaskTimeline;
use App\Models\TaskType;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\DB;
use JsonIncrementalParser;
use Illuminate\Support\Facades\Storage;
use stdClass;
use Illuminate\Support\Facades\URL;

class TaskController extends Controller
{
    use ApiResponser;

    public function person($companyId)
    {
        $person = Person::whereHas('work_contract', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })
            ->where('status', 'Activo')
            ->orderBy('first_name')
            ->get([
                "id as value",
                DB::raw('UPPER(CONCAT_WS(" ", first_name, second_name, first_surname, second_surname)) as text')
            ]);
        return $this->success($person);
    }

    public function getAsignadas($id)
    {
        return $this->success(
            Task::with('realizador', 'types')
                ->where('id_asignador', $id)
                ->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1))
        );
    }

    public function personTasks(Request $request)
    {
        return $this->success(Task::with('asignador', 'types')
            ->where('id_realizador', $request->person_id)
            ->when($request->estado, function ($q, $fill) {
                $q->where('estado', '=', $fill);
            })
            ->when($request->except, function ($q, $fill) {
                $q->where('id', '!=', $fill)
                    ->where('estado', '!=', 'Archivada')
                    ->where('estado', '!=', 'Finalizado');
            })
            ->when($request->max, function ($q, $fill) {
                $q->limit($fill);
            })
            ->orderByDesc('fecha')
            ->orderBy('hora')
            ->get());
    }

    public function getArchivadas(Request $request)
    {
        return $this->success(Task::with('asignador', 'types')
            ->where('estado', 'Archivada')
            ->where('id_realizador', $request->person_id)
            /* ->orWhere('id_asignador', $request->person_id) */
            ->orderByDesc('fecha')
            ->orderBy('hora')
            ->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1)));
    }

    public function statusUpdate(Request $request)
    {
        $task = Task::where('id', $request->id)->with('realizador')->first();
        Task::where('id', $request->id)->update(['estado' => $request->status]);
        $person = Person::where('id', $task->realizador->id)->fullName()->first();
        Alert::create([
            'user_id' => $task->id_asignador,
            'person_id' => $task->id_realizador,
            'type' => 'Cambio de estado de la tarea',
            'description' => $person->full_names . ' ha cambiado el estado de la tarea ' . strtolower($task->titulo) . ' a ' . strtolower($request->status),
            'url' => '/' . 'task/' . $task->id,
            'icon' => 'fas fa-arrow-right',

        ]);
        TaskTimeline::create([
            'icon' => 'fas fa-arrow-right',
            'title' => 'Cambio de estado',
            'description' => $person->full_names . ' cambió el estado a ' . strtolower($request->status),
            'task_id' => $request->id,
            'person_id' => $task->id_asignador,

        ]);
        return $this->success('Actualizado con éxito');
    }

    public function taskView($id)
    {
        return $this->success(
            Task::where('id', $id)
                ->with('asignador', 'realizador', 'adjuntos', 'comment', 'types', 'timeline')
                ->first()
        );
    }

    public function updateComments(Request $request)
    {
        return $this->success(TaskComment::with('autor')->where('task_id', $request->id)->get());
    }

    public function newComment(Request $request)
    {
        $task = Task::where('id', $request->task_id)->with('realizador')->first();
        $person = Person::where('id', $request->person_id)->first();
        TaskComment::create($request->all());
        if ($task->id_realizador == $person->id) {
            $user_id = $task->id_asignador;
            $this->alertComment($user_id, $person, $task);
        } else if ($task->id_asignador == $person->id) {
            $user_id = $task->id_realizador;
            $this->alertComment($user_id, $person, $task);
        }
        return $this->success(
            TaskComment::where('task_id', $request->task_id)->with('autor')->get()
        );
    }

    private function alertComment($user_id, $person, $task)
    {
        $person_ = Person::where('id', $person->id)->fullName()->first();
        Alert::create([
            'user_id' => $user_id,
            'person_id' => $person->id,
            'type' => 'Nuevo comentario',
            'description' => $person_->full_names
                . ' ha publicado un nuevo comentario en la tarea: '
                . strtolower($task->titulo),
            'url' => '/' . 'task/' . $task->id,
            'icon' => 'fas fa-comments',
        ]);
        TaskTimeline::create([
            'icon' => 'fas fa-comments',
            'title' => 'Nuevo comentario',
            'description' => $person_->full_names
                . ' publicó un nuevo comentario',
            'task_id' => $task->id,
            'person_id' => $person->id,
        ]);
    }

    public function deleteComment($id)
    {
        $comment = TaskComment::where('id', $id)->first();
        $task_id = $comment->task_id;
        $person = Person::where('id', $comment->person_id)->fullName()->first();
        $comment->delete();
        TaskTimeline::create([
            'icon' => 'fas fa-trash',
            'title' => 'Comentario eliminado',
            'description' => $person->full_names . ' eliminó un comentario ',
            'task_id' => $task_id,
            'person_id' => $person->id,

        ]);
        return $this->success(
            TaskComment::where('task_id', $task_id)->with('autor')->get()
        );
    }

    public function new(Request $request)
    {
        $data = $request->all();
        $task = Task::create($data);
        $task_id = $task->id;
        if ($request->has('files')) {
            $data_files = $request->get('files');
            foreach ($data_files as $file) {
                $type = '.' . $file['type'];
                if ($file['type']) {
                    if ($file['type'] == 'jpeg' || $file['type'] == 'jpg' || $file['type'] == 'png') {
                        $base64 = saveBase64($file['base64'], 'task/', true, $type);
                        $save = URL::to('/') . '/api/image?path=' . $base64;
                    } else {
                        $base64 = saveBase64File($file['base64'], 'task/', false, '.pdf');
                        $save = URL::to('/') . '/api/file?path=' . $base64;
                    }
                    TaskFile::create([
                        'name' => $file['name'],
                        'type' => $file['type'],
                        'task_id' => $task_id,
                        'file' => $save
                    ]);
                }
            }
        }

        if ($request->category == 'Negocios') {
            BusinessTask::create([
                'task_id' => $task_id,
                'business_id' => $request->business_id
            ]);
        }
        $asignador = Person::where('id', $data['id_asignador'])->fullName()->first();
        Alert::create([
            'user_id' => $data['id_realizador'],
            'person_id' => $data['id_asignador'],
            'type' => 'Nueva tarea',
            'description' => $asignador->full_names . ' te ha asignado una nueva tarea.',
            'url' => '/' . 'task/' . $task_id,
            'icon' => 'fas fa-tasks',

        ]);
        TaskTimeline::create([
            'icon' => 'fas fa-tasks',
            'title' => 'Nueva tarea',
            'description' => $asignador->full_names . ' creó la tarea.',
            'task_id' => $task_id,
            'person_id' => $asignador->id,

        ]);

        return $this->success('Creado con éxito', $task_id);
    }
}
