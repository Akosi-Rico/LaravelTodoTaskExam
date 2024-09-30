<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\ProcessRequest;
use App\Models\Manage\Task;
use App\Services\Models\TaskService;

class TaskController extends Controller
{
    public function __construct(private TaskService $taskService)
    {
    }

    public function index()
    {
        $data = $this->taskService->loadDetail();
        return view("modules.index", compact('data'));
    }

    public function store(ProcessRequest $request, Task $task)
    {
       $this->taskService->create(request()->payload, $task);
    }

    public function update(ProcessRequest $requests, Task $task)
    {
        $this->taskService->update(request()->payload, $task);
    }

    public function destroy(Task $task)
    {
        $this->taskService->delete($task);
    }

    public function loadTask(Task $task)
    {
       return $this->taskService->loadTable(request()->all(), $task);
    }

    public function upload()
    {
        return $this->taskService->upload(request()->file('file'));
    }

    public function search(Task $task) 
    {
        return $this->taskService->searchTitle(request()->keyword, $task);
    }

    public function handleErrorPage($code)
    {
        abort($code);
    }
}
