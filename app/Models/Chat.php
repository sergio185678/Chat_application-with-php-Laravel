<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Msg;
use Illuminate\Support\Facades\Auth;

class Chat extends Model
{
    //es necesario esto para ORM
    public function users(){
        return $this->belongsToMany(User::class);
    }

    //es necesario esto para ORM
    public function msgs(){
        return $this->hasMany(Msg::class);
    }

    //esta contando la cantidad de mensajes nuevos por chat
    static public function chat_update($chats){
        $total_msg=[];
        foreach($chats as $chat){
            $i=0;
            foreach($chat->msgs as $msg){
                //con seen=0 confirma que sean mensajes nuevos
                //con lo de al lado mira que esos mensajes no sean nuestros y no los cuente
                if($msg->seen==0 && $msg->user_id!=Auth::user()->id){
                    $i++;
                    $total_msg[$chat->id]=$i;
                }
            }
        }
        return $total_msg;
    }
}
