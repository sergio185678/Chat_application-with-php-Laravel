<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Msg;
use App\Models\Chat;
use Illuminate\Support\Facades\Auth;

class MsgController extends Controller
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
            'msg'=>'required',
            'c_id'=>'required'
        ]);
        $cu=Auth::user()->id;
        $msg=new Msg;
        $msg->msg=$request->msg;
        $msg->user_id=$cu;
        $msg->chat_id=$request->c_id;
        $msg->seen=0;
        $msg->save();
        if(!empty($msg)){
            $resp["status"]=1;
            $resp["txt"]="Successfully Create A New Msg";
            $resp["obj"]=$msg;

            $c=Chat::find($request->c_id);
            if(count($c->msgs)>1){
                $resp["fst"]=0;
            }else{
                $resp["fst"]=1;
            }
        } else{
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

    public function message_list(Request $request)
    {
        $chat=Chat::find($request->c_id);
        if($request->limit>10){
            $msgs=$chat->msgs()->take($request->limit)->skip($request->limit-10)->orberBy("id","asc")->get();
        } else{
            $msgs=$chat->msgs()->take($request->limit)->orderBy("id","asc")->get();
        }
        $me=Auth::user();
        $resp['status']=1;
        $resp['txt']=(string) view ('layouts.msg_list',compact("msgs","me"));
        return json_encode($resp);
    }

    public function new_message_list(Request $request)
    {
        $chat=Chat::find($request->c_id);
        $me=Auth::user();
        if($request->me==1){
            $msgs=$chat->msgs()->where("seen","=",0)->where("user_id","=",$me->id)->orderBy("id","desc")->take(1)->get();
        } else{
            $msgs=$chat->msgs()->where("seen","=",0)->where("user_id","<>",$me->id)->get();
        }
        if(count($msgs)>0){
            $resp["status"]=1;
            $resp["txt"]=(string) view("layouts.msg_list",compact("msgs","me"));
        }else{
            $resp["status"]=2;
        }
        return json_encode($resp);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
