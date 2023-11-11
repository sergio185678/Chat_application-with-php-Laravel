<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $users= User::allUser();
        $chats=Auth::user()->chats()->orderby("id","desc")->get();
        $me =Auth::user();
        $msgs=[];
        return view('home',compact("users","chats","me","msgs"));
    }
}
