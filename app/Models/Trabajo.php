<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trabajo extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    public function secciones()
    {
        return $this->hasMany(Seccion::class);
    }

    public function galerias()
    {
        return $this->hasMany(Galeria::class);
    }
    
}
