<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Destinozona extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function destino()
    {
        return $this->belongsTo(Destino::class, 'destino_id', 'id')->orderBy('nombre');
    }

    public function zona()
    {
        return $this->belongsTo(Zona::class, 'zona_id', 'id')->orderBy('nombre');
    }
}
