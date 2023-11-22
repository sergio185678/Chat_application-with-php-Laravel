<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\ActiveChat;

class ActiveChatController extends Controller
{
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

    public function set_active(Request $request){
        $cu=Auth::user()->id;
        $chk=ActiveChat::where("user_id","=",$cu)->first();//obtengo la primera fila  que cumpla eso

        //guarda los cambios si esta escribiendo o no la persona en la tabla activechat
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

    //se encarga de saber que usuarios estan escribiendo en un chat en especifico
    public function check_active(Request $request){
        $cu=Auth::user()->id;
        $chk=ActiveChat::where("chat_id","=",$request->c_id)->where("typing","=",1)->get();

        $usr=[];//crea un arreglo por si hay mas de una persona escribiendo
        if(count($chk)>0){
            foreach($chk as $value){//almacena todos los nombres de usuario usando su id
                $u = User::find($value->user_id);
                if($u->id != $cu){
                    $usr[]=$u->name;
                }
            }
            if(count($usr)==1 && $usr != Auth::user()->name && $usr!=null){
                //en caso que solo 1 esta escribiendo solo retorna el nombre de esa persona
                $resp["user_name"]=$usr;
                $resp["txt"]=1;
            }elseif(count($usr)>1){
                //en caso haya mas le une en un string todos
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
