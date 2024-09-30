<?php

namespace App\Models\Manage;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Manage\Attachment;
use App\Models\Manage\Tags;

class Task extends Model
{
    use HasFactory;

    protected $guarded = [];

    const Archive = "A";
    const Todo = "T";
    const InProgress = "I";
    const Done = "D";

    public function attachments()
    {
        return $this->hasMany(Attachment::class,"task_id", "id");
    }

    public function tags()
    {
        return $this->hasMany(Tags::class, "task_id", "id");
    }

    public static function search($keyword)
    {
        $statement = self::select("title as code", "title as label")
            ->where("title", "like", "%".$keyword."%")
            ->get();

        if (empty($statement)) {
            return false;
        }
        
        return response()->json(["data" => $statement]);
    }
}
