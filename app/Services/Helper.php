<?php

namespace App\Services;

use App\Contracts\TaskInterface;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use App\Services\JsonOutput;

trait Helper
{
    public static function loadResponse($message, $code, TaskInterface $response)
    {
        return $response->output($message, $code);
    }

    public static function loadDetails()
    {
        return [
            "statusOption" => config("taskOption.statusOptions"),
            "priorityOption" => config("taskOption.priorityLevelOption"),
            "tagsOption" => config("taskOption.tagsOptions"),
            "taskStoreUrl" => route("task.store"),
            "taskUpdateUrl" => route("task.update", ""),
            "currentUser" => auth()->user()->name ?? null,
            "currentDate" => now()->format('F j, Y'),
            "taskdataTableUrl" => route("load.task.table"),
            "attachmentUrl" => asset("temporary"),
            "logoutUrl" => route("logout.account"),
            "loginUrl" => route("login.index"),
            "searchTitleUrl" => route("search.title"),
            "uploadUrl" => route("upload.file"),
            "processPageUrl" => route("process.page",""),
            "isGuestUser" => auth()->user()->email == "guest@example.com" ? true : false
        ];
    }

    public static function moveFile($results) 
    {
        $fileName = [];
        if (empty($results)) {
            return false;
        }

        foreach ($results as $file) {
            if ($file->getClientOriginalName()) {
                $fileName[] = $file->getClientOriginalName();
                Storage::disk("temporary")->put($file->getClientOriginalName(), file_get_contents($file));
            }
        }

        return self::loadResponse($fileName, Response::HTTP_OK, new JsonOutput);
    }
}
