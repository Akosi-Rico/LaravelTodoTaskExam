<?php

namespace App\Models\Manage;

use App\Services\Helper;
use App\Services\JsonOutput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class Attachment extends Model
{
    use HasFactory, Helper;

    protected $guarded = [];

    public static function canCreate($id, $data)
    {
        try {
            DB::beginTransaction();

            if ((empty($id) && empty($data))) {
                return false;
            }

            $tempData = [];
            collect($data)->map(function($file) use($id, &$tempData) {
                $tempData[] = [
                    "task_id" => $id,
                    "file_name" => $file
                ];
            });

            self::insert($tempData);

            DB::commit();
            return self::loadResponse("Transaction Successfully", Response::HTTP_OK, new JsonOutput);
        } catch(\Throwable $th) {
            DB::rollback();
            return self::loadResponse($th->getMessage(), Response::HTTP_BAD_REQUEST, new JsonOutput);
        }
    }

    public static function canUpdate($id, $data)
    {
        try {
            DB::beginTransaction();

            if ((empty($id) && empty($data))) {
                return false;
            }

            $tempData = [];

            self::where("task_id", $id)->delete();
            collect($data)->map(function($file) use($id, &$tempData) {
                $tempData[] = [
                    "task_id" => $id,
                    "file_name" => $file
                ];
            });

            self::insert($tempData);

            DB::commit();
            return self::loadResponse("Transaction Successfully", Response::HTTP_OK, new JsonOutput);
        } catch(\Throwable $th) {
            DB::rollback();
            return self::loadResponse($th->getMessage(), Response::HTTP_BAD_REQUEST, new JsonOutput);
        }
    }
}
