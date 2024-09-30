<?php

namespace App\Services;

use App\Contracts\TaskInterface;

class JsonOutput implements TaskInterface
{
    public function output($message, $code) 
    {
        if ((empty($message) || empty($code))) {
            return false;
        }

        return response()->json([
            "message" => $message,
        ], $code);
    }
}
