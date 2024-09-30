<?php

namespace App\Models;

use App\Observers\UserObserver;
use App\Services\JsonOutput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Services\Helper;
#[ObservedBy([UserObserver::class])]
class User extends Authenticatable
{
    use HasFactory, Notifiable, Helper;

    protected $guard_name = "web";

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public static function registerNewUser($request)
    {
        try {
            DB::beginTransaction();

            if (empty($request)) {
                return false;
            }

            self::create([
                "name" => request()->payload["name"],
                "email" => request()->payload["email"],
                "password" => Hash::make(request()->payload["password"]),
            ]);

            DB::commit();

            return self::loadResponse('Transaction Successfully Completed!', Response::HTTP_OK, new JsonOutput);
        } catch(\Throwable $th) {
            DB::rollback();
            return self::loadResponse($th->getMessage(), Response::HTTP_BAD_REQUEST, new JsonOutput);
        }
    }

    public static function login($request) 
    {
        try {
            if (empty($request)) {
                return false;
            }

            auth()->attempt(["email" => $request["email"], "password" => $request["password"]]);
            if (empty(auth()->check())) {
                throw new \Exception("Email & Password given is not registered");
            }

           return self::loadResponse("Successfully Login!", Response::HTTP_OK, new JsonOutput);
        } catch(\Throwable $th) {
            return self::loadResponse($th->getMessage(), Response::HTTP_BAD_REQUEST, new JsonOutput);
        }
    }
}
