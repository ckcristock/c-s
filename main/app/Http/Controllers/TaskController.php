<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;
use App\Models\Task;
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
        $person = Person::whereHas('work_contract', function($q) use ($companyId){
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
            ->get());
    }

    public function personTasks(Request $request) {
        return $this->success(Task::with('asignador')
            ->where('id_realizador', $request->person_id)
            ->when($request->estado, function($q, $fill) {
                $q->where('estado', '=', $fill);
            })
            ->get());
    }

    public function statusUpdate(Request $request)
    {
        Task::where('id', $request->id)->update(['estado' => $request->status]);
        return $this->success('Actualizado con éxito');
    }

    public function updateArchivado($id)
    {
        DB::table('tasks')
            ->where('id', $id)
            ->update(['estado' => "Archivada"]);
    }

   

    public function taskView($id)
    {
        $task = DB::table('tasks')
            ->join('people', 'tasks.id_asignador', '=', 'people.id')
            ->where('tasks.id', $id)
            ->get(['tasks.*', 'people.first_name', 'people.second_name', 'people.first_surname']);
        $task2 = DB::table('tasks')
            ->join('people', 'tasks.id_realizador', '=', 'people.id')
            ->where('tasks.id', $id)
            ->get(DB::raw('CONCAT_WS(" ",first_name,second_name,first_surname) as name '));
        return $this->success($task, $task2);
    }

    public function newComment($comment)
    {
        $commentjson = json_decode($comment);
        $new = DB::table('comentarios_tareas')
            ->insert([
                'id_person' => $commentjson->{'id_person'},
                'fecha' => $commentjson->{'fecha'},
                'comentario' => $commentjson->{'comentario'},
                'id_task' => $commentjson->{'id_task'},
            ]);
        return $this->success($new);
    }
    public function getComments($idTask)
    {
        $comments = DB::table('comentarios_tareas')
            ->join('tasks', 'tasks.id', '=', 'comentarios_tareas.id_task')
            ->join('people', 'people.id', '=', 'comentarios_tareas.id_person')
            ->where('comentarios_tareas.id_task', $idTask)
            ->get(['comentarios_tareas.*', DB::raw('CONCAT_WS(" ",first_name,second_name,first_surname) as name ')]);
        return $this->success($comments);
    }

    public function deleteComment($commentId)
    {
        $delete = DB::table('comentarios_tareas')
            ->where('comentarios_tareas.id', $commentId)
            ->delete();
        return $this->success($delete);
    }

    public function deleteTask($idTask)
    {
        $delete = DB::table('tasks')
            ->where('tasks.id', $idTask)
            ->delete();
        return $this->success($delete);
    }

    public function adjuntosTask($idTask)
    {
        $myArray = [];
        $adjuntos = DB::table('adjuntos_tasks')
            ->join('tasks', 'tasks.id', '=', 'adjuntos_tasks.id_task')
            ->where('tasks.id', $idTask)
            ->get('adjuntos_tasks.*');

        $json = json_decode($adjuntos, true);
        foreach ($json as &$value) {
            $nombre = $value['nombre'];
            $url1 = $value['file'];
            /* $file = (base64_encode(file_get_contents("C:/laragon/www/core-back/main/storage/app/taskmanager/$idTask/$nombre"))); */
            $file = (base64_encode(file_get_contents($url1)));
            $value["fileview"] = $file;
            //return array($file);
        }
        /* for ($i = 0; $i < count($json); $i++) {
            $nombre = $json[$i]['nombre'];
            $file = json_encode(base64_encode(file_get_contents("C:/laragon/www/ateneo-server/main/storage/app/taskmanager/$idTask/$nombre")));            
            //$contents = base64_decode(Storage::disk('taskmanager')->get("/$idTask/$nombre"));
            //array_push($json, array('fileview' => "$file"));
            return $json;
        }
         */
        return $this->success($json);
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
