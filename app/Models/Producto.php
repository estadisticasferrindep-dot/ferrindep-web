<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    protected $appends = ['imagen_url']; //lo que quiero pasarle a vue.js



    
    public function getImagenUrlAttribute()
    {
        return asset(Storage::url($this->imagen));
    }
    




    // public function getTprecioAttribute()
    // {   $user =auth()->guard('usuario')->user();

    //     if($user){
    //         if($user->tipo_cliente == "publico"){
    //             return $this->precio;
    //         }
    //         if($user->tipo_cliente == "mayorista"){
    //             return $this->precio_mayorista;
    //         }
    //         if($user->tipo_cliente == "especial"){
    //             return $this->precio_especial;
    //         }
    //         if($user->tipo_cliente == "gremio"){
    //             return $this->precio_gremio;
    //         }
    //     }
    //     return $this->precio;

    // }

    // public function getTprecioAnteriorAttribute()
    // {   $user =auth()->guard('usuario')->user();

    //     if($user){
    //         if($user->tipo_cliente == "publico"){
    //             return $this->precio_anterior;
    //         }
    //         if($user->tipo_cliente == "mayorista"){
    //             return $this->precio_anterior_mayorista;
    //         }
    //         if($user->tipo_cliente == "especial"){
    //             return $this->precio_anterior_especial;
    //         }
    //         if($user->tipo_cliente == "gremio"){
    //             return $this->precio_anterior_gremio;
    //         }
    //     }
    //     return $this->precio_anterior;

    // }


    public function galerias()
    {
        return $this->hasMany(Galeria::class)->orderBy('orden');
    }


    public function diametros()
    {
        return $this->hasMany(Diametro::class);
    }


    public function rangos()
    {
        return $this->hasMany(Rango::class);
    }


    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id', 'id')->orderBy('orden');
    }
    public function familia()
    {
        return $this->belongsTo(Familia::class, 'familia_id', 'id')->orderBy('orden');
    }

    public function medidas()
    {
        return $this->belongsTo(Medida::class, 'medida_id', 'id')->orderBy('orden');
    }

    public function espesor()
    {
        return $this->belongsTo(Espesor::class, 'espesor_id', 'id')->orderBy('orden');
    }

    public function presentaciones()
    {
        return $this->hasMany(Presentacion::class);
    }

    // public function getPrecioAttribute($value)
    // {   
        
    //     $diams = $this->diametros;

    //     if(count($diams) >= 1){
    //         return $diams->first()->precio;
    //     }else{
    //         return $value;
    //     }


    //     //return 200;

    // }



    // public function colores()
    // {
    //     return $this->hasMany(ColorProducto::class);
    // }

    // public function galerias()
    // {
    //     return $this->hasMany(Galeria::class);
    // }

    
    
}
