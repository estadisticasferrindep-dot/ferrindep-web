<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MapeoUbicacion extends Model
{
    use HasFactory;

    protected $table = 'mapeo_ubicaciones';

    protected $fillable = [
        'ciudad_detectada',
        'destino_id'
    ];

    /**
     * Relación con el Destino (Zona de envío/Logística)
     */
    public function destino()
    {
        return $this->belongsTo(Destino::class, 'destino_id');
    }
}
