<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Chat;
use App\Models\User;

class Msg extends Model
{
    public function chat(){
        return $this->belongsTo(Chat::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}
