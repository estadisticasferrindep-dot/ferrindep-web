<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TarifaLogistica extends Model
{
    protected $table = 'tarifas_logistica';
    protected $fillable = ['nombre', 'monto'];

    public function zonas()
    {
        return $this->hasMany(MapeoZonaFlex::class, 'tarifa_id');
    }
}
