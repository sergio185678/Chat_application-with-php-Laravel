<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\ActiveChat;

class ActiveChatController extends Controller
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
            "c_id"=>"required"
        ]);
        $cu=Auth::user()->id;
        $chk=ActiveChat::where("user_id","=",$cu)->first();
        if(!empty($chk)){
            $chk->chat_id=$request->c_id;
            $chk->save();
        }else{
            $active=new ActiveChat;
            $active->chat_id=$request->c_id;
            $active->user_id=$cu;
            $active->typing=false;
            $active->save();
        }
        if(!empty($active)||!empty($chk)){
            $resp["status"]=1;
            $resp["txt"]="Success";
        }else{
            $resp["status"]=0;
            $resp["txt"]="Something went wrong!";
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

    public function set_active(Request $request){
        $cu=Auth::user()->id;
        $chk=ActiveChat::where("user_id","=",$cu)->first();

        if(!empty($chk)){
            $chk->typing=$request->con;
            $chk->save();
        }

        if(!empty($chk)){
            $resp["status"]=1;
            $resp["con"]=$request->con;
        }else{
            $resp["status"]=0;
        }
        return json_encode($resp);
    }

    public function check_active(Request $request){
        $cu=Auth::user()->id;
        $chk=ActiveChat::where("chat_id","=",$request->c_id)->where("typing","=",1)->get();

        $usr=[];
        if(count($chk)>0){
            foreach($chk as $value){
                $u = User::find($value->user_id);
                if($u->id != $cu){
                    $usr[]=$u->name;
                }
            }
            if(count($usr)==1 && $usr != Auth::user()->name && $usr!=null){
                $resp["user_name"]=$usr;
                $resp["txt"]=1;
            }elseif(count($usr)>1){
                $resp["user_name"]=implode(",",$usr);
                $resp["txt"]=1;
            }else{
                $resp["txt"]=0;
            }
        }else{
            $resp["txt"]=0;
        }

        if(!empty($usr)){
            $resp["status"]=1;
        }else{
            $resp["status"]=0;
            $resp["txt"]="something went wrong!";
        }
        return json_encode($resp);
    }
}
