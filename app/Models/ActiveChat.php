<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActiveChat extends Model
{
    use HasFactory;
    public $timestamps=false;
    protected $table="activechats";
}
