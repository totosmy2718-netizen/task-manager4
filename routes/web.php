<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});


// 認証が必要なルート
Route::middleware('auth')->group(function () {
    //検索フォームのルート
    Route::get('/tasks/search', [TaskController::class, 'search']);

    //CSV出力のルート
    // CSV出力
    Route::get('/tasks/export', [TaskController::class, 'export'])
        ->name('tasks.export');


    // カテゴリーのCRUDルート
    Route::resource('categories', CategoryController::class);

    // タスクのCRUDルート（仮ルートから置き換え）
    Route::resource('tasks', TaskController::class);


});
