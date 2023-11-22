<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Msg;
use App\Models\Chat;
use Illuminate\Support\Facades\Auth;

class MsgController extends Controller
{
    
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
        $msg->seen=0;//se estable predefinidamente que el mensaje no ha sido visto
        $msg->save();//se debe guardar asi con ORM
        if(!empty($msg)){
            $resp["status"]=1;
            $resp["txt"]="Successfully Create A New Msg";
            $resp["obj"]=$msg;

            $c=Chat::find($request->c_id);
            //fst se refiere que si solo hay un mensaje en total, o si hay mas de uno 
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
    
    //obtiene la lista de mensajes apartir del limite establecido
    public function message_list(Request $request)
    {
        $chat=Chat::find($request->c_id);
        if($request->limit>10){
            $total=$chat->msgs()->orderBy("id","desc")->get();
            if(count($total)<$request->limit+10){
                $resp["end"]=true;
            }
            $msgs=$chat->msgs()->take($request->limit)->skip($request->limit-10)->orderBy("id","desc")->get();
        } else{
            $msgs=$chat->msgs()->take($request->limit)->orderBy("id","desc")->get();
        }
        $me=Auth::user();
        if(!isset($resp["end"])){
            $resp["end"]=false;
        }
        $resp['status']=1;
        $resp['txt']=(string) view ('layouts.msg_list',compact("msgs","me"));//retorno como un html con nuevos mensajes
        //para que el javascript pueda agregar estos nuevos mensajes ahi
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

    public function message_seen(Request $request){
        $ck=Msg::where("seen","=",0)->where("chat_id","=",$request->c_id)->where("user_id","<>",Auth::user()->id)->update(["seen"=>1]);
        if($ck){
            $resp["status"]=1;
        }else{
            $resp["status"]=0;
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
