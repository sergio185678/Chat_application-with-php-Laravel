<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Msg;
use Illuminate\Support\Facades\Auth;

class Chat extends Model
{
    public function users(){
        return $this->belongsToMany(User::class);
    }

    public function msgs(){
        return $this->hasMany(Msg::class);
    }

    static public function chat_update($chats){
        $total_msg=[];
        foreach($chats as $chat){
            $i=0;
            foreach($chat->msgs as $msg){
                if($msg->seen==0 && $msg->user_id!=Auth::user()->id){
                    $i++;
                    $total_msg[$chat->id]=$i;
                }
            }
        }
        return $total_msg;
    }
}
