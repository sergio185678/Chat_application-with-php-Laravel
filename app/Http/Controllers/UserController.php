<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Session;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    public function store(Request $request)
    {
        $this->validate($request,[
            'name'=>"required",
            'email'=>"required|unique:users,email,".Auth::user()->id,
        ]);
        $user=User::find(Auth::user()->id);
        $user->name=$request->name;
        $user->email=$request->email;
        $user->save();
        Session::flash("success","Save Successfully");
        return redirect('/profile');
    }

    public function profile()
    {
        $me=Auth::user();
        $users=User::allUser();
        return view('profile',compact('me','users'));
    }

    //funcion para actualizar la foto de perfil
    public function pic(Request $request)
    {
        $this->validate($request,[
            'pic_file'=>"required|image|mimes:jpeg,png,jpg"
        ]);
        $user=User::find(Auth::user()->id);
        $imageName=$user->id.'-'.uniqid().'.'.$request->file('pic_file')->getClientOriginalExtension();
        $request->file('pic_file')->move(base_path().'/public/img/',$imageName);
        $user->pic=$imageName;
        $user->save();
        Session::flash('success',"Image Updated Successfully");
        return redirect('/profile');
    }

    //función para actualizar contraseña
    public function pass_update(Request $request)
    {
        $this->validate($request,[
            'new_password'=>"required|min:4|confirmed",
        ]);
        $user=User::find(Auth::user()->id);
        $user->password=bcrypt($request->new_password);
        $user->save();
        Session::flash("success","Password Updated Successfully");
        return redirect('/profile');
    }
}
