<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string("title", 199)->index();
            $table->string("description", 199);
            $table->date("due_date")->index()->nullable(true);
            $table->date("archive_date")->index()->nullable(true);
            $table->string("priority_level")->default("PL")
                ->comment("
                    PL - Low, 
                    PM - Meddium, 
                    PH - High
                ");
            $table->string("status")->default("I")
                ->comment("
                    T - Todo
                    I - In-progress,
                    D - Done, 
                    A - Archive
                ");
            $table->bigInteger("user_id")->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
