<?php

namespace App\Observers;

use App\Models\User;
class UserObserver
{
    public function created(User $model): void
    {
        if (!empty($model)) {
            auth()->attempt(["email" => request()->payload["email"], "password" => request()->payload["password"]]);
        }
    }
}
