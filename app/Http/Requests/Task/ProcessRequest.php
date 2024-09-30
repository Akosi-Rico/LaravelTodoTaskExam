<?php

namespace App\Http\Requests\Task;

use App\Models\Management\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProcessRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = (isset(request()->payload["id"]) ? request()->payload["id"] : null);
        return [
            'payload.title' => ['required', 'max:100',"unique:tasks,title,$id"],
            'payload.description' => ['required'],
            'payload.status' => ['required'],
            'payload.priority_level' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'payload.title.required' => "Title is required, Please fill out the required field!",
            'payload.title.max' => "Title may not be greater than 100 characters..",
            'payload.title.unique' => "This title has already been taken",
            'payload.description.required' => "Description is required, Please fill out the required field!",
            'payload.status.required' => "Status is required, Please fill out the required field!",
            'payload.priority_level.required' => "Priority Level is required, Please fill out the required field!",
        ];
    }
}
