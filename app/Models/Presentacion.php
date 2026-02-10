<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presentacion extends Model
{
    use HasFactory;


    protected $guarded = [];

    protected $table = "presentaciones";

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
