<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MapeoZonaFlex extends Model
{
    protected $table = 'mapeo_zonas_flex';
    protected $fillable = ['nombre_busqueda', 'tarifa_id'];

    public function tarifa()
    {
        return $this->belongsTo(TarifaLogistica::class, 'tarifa_id');
    }
}
