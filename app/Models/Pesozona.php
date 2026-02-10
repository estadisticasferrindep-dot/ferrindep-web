<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesozona extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function zona()
    {
        return $this->belongsTo(Zona::class, 'zona_id', 'id')->orderBy('nombre');
    }
    
}
