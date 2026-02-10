<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    
    public function familia()
    {
        return $this->belongsTo(Familia::class, 'familia_id', 'id')->orderBy('orden');
    }

    public function tieneProductosFamilia($familia_id)
    {
        
        return count(Producto::where('familia_id', $familia_id)->where('show', 1)->where('categoria_id', $this->id)->get()) > 0;

        // $productos=  $this->hasMany(Producto::class);

        // $f_id = $familia_id;

        // $new_array = array_filter($productos, function($obj){
        //     if (isset($obj->admins)) {
        //         foreach ($obj->admins as $admin) {
        //             if ($admin->familia_id == $f_id) return false;
        //         }
        //     }
        //     return true;
        // });

        // return $new_array;*/

    }
}
