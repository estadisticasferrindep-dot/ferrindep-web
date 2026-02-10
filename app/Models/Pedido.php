<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function itemsPedidos()
    {
        return $this->hasMany(\App\Models\Itemspedido::class, 'pedido_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
