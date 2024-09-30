<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Manage\TaskController;
use Illuminate\Support\Facades\Route;




Route::middleware('isoGuest')->group(function () {
    Route::resource("/login", LoginController::class);
    Route::post("/login/process", [LoginController::class, "login"])->name("login.process");
});

Route::middleware(["isoLogin"])->group(function () {
    Route::resource("/task",TaskController::class);
    Route::post("/task/load/task",[TaskController::class, 'loadTask'])->name("load.task.table");
    Route::post("/task/upload/file",[TaskController::class, 'upload'])->name("upload.file");   
    Route::post("/task/search/title",[TaskController::class, 'search'])->name("search.title");  
    Route::post("/logout", [LoginController::class, "logout"])->name("logout.account");
    Route::get("/process/page/{code}",[TaskController::class, 'handleErrorPage'])->name("process.page");  
});
