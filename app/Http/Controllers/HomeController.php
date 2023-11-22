<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Chat;

class HomeController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $users= User::allUser();//llama a una función del modelo User
        //este es un ejemplo de ORM, revisar la funcion chats() para saber como construir los objetos
        $chats=Auth::user()->chats()->orderby("id","desc")->get();
        $me =Auth::user(); //devuelve el objeto autenticado del usuario logeado
        $msgs=[];
        $total_msg=Chat::chat_update($chats);
        return view('home',compact("users","chats","me","msgs","total_msg"));
    }
}
