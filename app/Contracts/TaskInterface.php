<?php

namespace App\Contracts;

interface TaskInterface
{
    public function output($message, $code);
}
