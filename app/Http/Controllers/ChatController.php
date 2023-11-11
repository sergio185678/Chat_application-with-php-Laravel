<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Chat;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'users'=> "required"
        ]);
        $chk_chats=Auth::user()->chats;
        $already_exit="";
        foreach ($chk_chats as $ct) {
            $un=[];
            $already_exit=false;
            foreach ($ct->users as $u) {
                if(Auth::user()->id != $u->id){
                    $un[]=(string)$u->id;
                }
            }
            if($request->users == $un){
                $already_exit=true;
                break;
            }
            else{
                $already_exit=false;
            }
        }
        if(!$already_exit){
            $chat=new Chat;
            $chat->user_id=Auth::user()->id;
            $chat->save();
            $chat->users()->attach(Auth::user()->id);
            foreach ($request->users as $id) {
                $chat->users()->attach($id);
            }
            if(!empty($chat)){
                $resp['status']=1;
                $resp['txt']="Successfully created a new chat!";
                $resp['obj']=$chat;
                $resp['objusers']=$chat->users;
            }else{
                $resp['status']=0;
                $resp['txt']="Something went wrong!";
            }
        }else{
            $resp['status']=0;
            $resp['txt']="Chat already exist!";
        }
        return json_encode($resp);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
