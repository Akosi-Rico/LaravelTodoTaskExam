<?php

namespace App\Services\Models;

use App\Models\Manage\Attachment;
use App\Models\Manage\Task;
use App\Services\Helper;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use App\Services\JsonOutput;
class TaskService
{
    use Helper;

    public function create(array $request, Task $task): object
    {
        try {
            DB::beginTransaction();

            if (empty($request)) {
                return false;
            }

            $detail = $task::create([
                "title" => $request["title"],
                "description" => $request["description"],
                "due_date" => $request["due_date"],
                "archive_date" => ($request["status"] == $task::Archive 
                    ? now()->format("Y-m-d") 
                    : $request["archive_date"]),
                "priority_level" => $request["priority_level"],
                "status" => $request["status"],
                "user_id" => auth()->user()->id
            ]);

            Attachment::canCreate($detail->id, $request["attachment"]);

            DB::commit();
            return self::loadResponse("Transaction Successfully", Response::HTTP_OK, new JsonOutput);
        } catch(\Throwable $th) {
            DB::rollback();
            return self::loadResponse($th->getMessage(), Response::HTTP_BAD_REQUEST, new JsonOutput);
        }
    }

    public function update(array $request, Task $task): object
    {
        try {
            DB::beginTransaction();

            if (empty($request)) {
                return false;
            }

            $detail = $task::where("id", $request["id"])->first();
            if (empty($task)) {
                return false;
            }
            $detail->title = $request["title"];
            $detail->description = $request["description"];
            $detail->due_date = $request["due_date"];
            $detail->archive_date = ($request["status"] != Task::Archive 
                    ? null 
                    : now()->format("Y-m-d"));
            $detail->priority_level = $request["priority_level"];
            $detail->status = $request["status"];
            $detail->user_id = auth()->user()->id;
            $detail->save();

            Attachment::canUpdate($detail->id, $request["attachment"]);

            DB::commit();
            return self::loadResponse("Transaction Successfully", Response::HTTP_OK, new JsonOutput);
        } catch(\Throwable $th) {
            DB::rollback();
            return self::loadResponse($th->getMessage(), Response::HTTP_BAD_REQUEST, new JsonOutput);
        }
    }

    public function delete(Task $task): object
    {
        try {
            DB::beginTransaction();

            if (empty($task->id)) {
                return false;
            }

            $detail = $task::where("id", $task->id)->first();
            if (empty($task)) {
                return false;
            }

            $detail->delete();

            DB::commit();
            return self::loadResponse("Transaction Successfully", Response::HTTP_OK, new JsonOutput);
        } catch(\Throwable $th) {
            DB::rollback();
            return self::loadResponse($th->getMessage(), Response::HTTP_BAD_REQUEST, new JsonOutput);
        }
    }

    public function loadTable(array $request, Task $task)
    {
        try {
            $data = [];
            $records = 0;
            $tablecols = [
                0 => ['title'],
                1 => ['description'],
                2 => ['due_date'],
                3 => ['archive_date'],
                4 => ['priority_level'],
                7 => ['tasks.created_at'],
                8 => ['status'],
            ];

            $data = $task::select(
                "tasks.id",
                "title",
                "description",
                "due_date",
                "archive_date",
                "priority_level",
                "status",
                "tasks.created_at"
            )->with("attachments");
            
            self::searchColumn($data, $request);
            if (isset($request["order"][0]['column'])) {
                $data->orderBy($tablecols[$request["order"][0]['column']][0], $request["order"][0]['dir']);
            }

            if (!empty($request['tableFilter']['status'])) {
               $data = $data->where(function($q) use(&$request) {
                    $q->where("status", $request['tableFilter']['status'])
                        ->orWhere("priority_level", $request['tableFilter']['priority_level']);
                  });
            }

            $data =   $data->orderBy('tasks.id', 'asc')->get()->map(function($item) {
                $priority = collect(config("taskOption.priorityLevelOption"))
                    ->where("code", $item->priority_level)
                    ->first()["label"] ?? null;
                $status = collect(config("taskOption.statusOptions"))
                    ->where("code", $item->status)
                    ->first()["label"] ?? $item->status;
                $item->priority_level_description =  $priority;
                $item->status_description =  $status;
                $item->creation_date = $item->created_at->format("Y-m-d");

                return $item;
            });

            $records = $data->count();

            return [
                "draw" => $request["draw"],
                "recordsTotal" => $records,
                "recordsFiltered" => $records,
                "data" => $data
            ];
        } catch(\Throwable $th) {
            return self::loadResponse($th->getMessage(), Response::HTTP_BAD_REQUEST, new JsonOutput);
        }
    }

    public function upload($file): object
    {
        return self::moveFile($file);
    }

    public function loadDetail(): array
    {
        return self::loadDetails();
    }

    public function searchTitle(string $keyword, Task $task): object
    {
        return $task::search($keyword);
    }

    public function handleError(string $code)
    {
        return abort($code);
    }

    public static function searchColumn($dataset, $request)
    {
        if (isset($request["tableparam"])) {
            return collect($request["tableparam"])->map(function ($value) use (&$dataset) {
                if (!empty($value['column_value'])) {
                    $dataset->where($value['column_name'], 'like', "%".$value['column_value']."%");
                }
            });
        }
    }
}
