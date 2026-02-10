<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rango extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $appends = ['tprecio']; //lo que quiero pasarle a vue.js


    public function getTprecioAttribute()
    {   $user =auth()->guard('usuario')->user();

        if($user){
            return 1;
        }
        return 0;

    }


}
