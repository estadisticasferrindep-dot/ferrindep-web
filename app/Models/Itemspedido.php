<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Itemspedido extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
