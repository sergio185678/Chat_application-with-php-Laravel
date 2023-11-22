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

//carga primero el welcome
//sino esta autentificado carga solo el login y register
//con autentificado puede cargar el home

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
//el /home es la ruta, lo de arriba se refiere a la clase controladoe y llamas a la funcion index,
//por costumbre siempre poner un name igual a la ruta

Route::get('/profile',[App\Http\Controllers\UserController::class, 'profile'])->name('profile');
Route::post('/profile',[App\Http\Controllers\UserController::class, 'store'])->name('profile-save');
Route::post('/pic',[App\Http\Controllers\UserController::class, 'pic'])->name('pic-save');
Route::post('/password-update',[App\Http\Controllers\UserController::class, 'pass_update'])->name('pass-save');

Route::post('/chat', [App\Http\Controllers\ChatController::class, 'store']);
Route::get('/chat-update', [App\Http\Controllers\ChatController::class, 'chat_update']);

Route::post('/message', [App\Http\Controllers\MsgController::class, 'store']);
Route::post('/message-list', [App\Http\Controllers\MsgController::class, 'message_list']);
Route::post('/new-message-list', [App\Http\Controllers\MsgController::class, 'new_message_list']);
Route::post('/message-seen', [App\Http\Controllers\MsgController::class, 'message_seen']);

Route::post('/active', [App\Http\Controllers\ActiveChatController::class, 'store']);
Route::post('/set-active', [App\Http\Controllers\ActiveChatController::class, 'set_active']);
Route::post('/check-active', [App\Http\Controllers\ActiveChatController::class, 'check_active']);