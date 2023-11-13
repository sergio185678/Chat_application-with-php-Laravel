<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
//el /home es la ruta, lo de arriba se refiere a la clase controladoe y llamas a la funcion index,
//por costumbre siempre poner un name igual a la ruta
Route::post('/chat', [App\Http\Controllers\ChatController::class, 'store']);

Route::post('/message', [App\Http\Controllers\MsgController::class, 'store']);
Route::post('/message-list', [App\Http\Controllers\MsgController::class, 'message_list']);
Route::post('/new-message-list', [App\Http\Controllers\MsgController::class, 'new_message_list']);
Route::post('/message-seen', [App\Http\Controllers\MsgController::class, 'message_seen']);