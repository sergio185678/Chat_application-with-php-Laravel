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
}
