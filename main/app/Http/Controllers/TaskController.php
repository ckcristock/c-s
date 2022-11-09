<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\TaskComment;
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
            ->get(['id as value', DB::raw('CONCAT_WS(" ",first_name,second_name,first_surname) as text ')]);
        return $this->success($person);
    }

    public function getAsignadas($id)
    {
        return $this->success(
            Task::with('realizador')
                ->where('id_asignador', $id)
                ->get()
        );
    }

    public function personTasks(Request $request)
    {
        return $this->success(Task::with('asignador')
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

    public function statusUpdate(Request $request)
    {
        Task::where('id', $request->id)->update(['estado' => $request->status]);
        return $this->success('Actualizado con éxito');
    }

    public function taskView($id)
    {
        return $this->success(
            Task::where('id', $id)
                ->with('asignador', 'realizador', 'adjuntos', 'comment')
                ->first()
        );
    }

    public function newComment(Request $request)
    {
        TaskComment::create($request->all());
        return $this->success(
            TaskComment::where('task_id', $request->task_id)->with('autor')->get()
        );
    }

    public function deleteComment($id)
    {
        $comment = TaskComment::where('id', $id)->first();
        $task_id = $comment->task_id;
        $comment->delete();
        return $this->success(
            TaskComment::where('task_id', $task_id)->with('autor')->get()
        );
    }

    public function new(Request $request)
    {
        $data = $request->all();
        $type = '.' . $request->type;
        if ($request->type) {
            if ($request->type == 'jpeg' || $request->type == 'jpg' || $request->type == 'png') {
                $base64 = saveBase64($data["adjuntos"], 'task/', true, $type);
                $data['adjuntos'] = URL::to('/') . '/api/image?path=' . $base64;
            } else {
                $base64 = saveBase64File($data["adjuntos"], 'task/', false, '.pdf');
                $data['adjuntos'] = URL::to('/') . '/api/file?path=' . $base64;
            }
        }
        if ($request->link) {
            $data['link'] = str_replace('_', '/', $data['link']);
        }
        Task::create($data);
        $id_task = DB::getPdo()->lastInsertId();
        return $this->success('Creado con éxito', $id_task);
    }
}
