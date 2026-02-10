<?php

namespace App\Models;
use Illuminate\Support\Facades\Storage;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $appends = ['imagen_url'];

    protected $table= "colores";

    public function getImagenUrlAttribute()
    {
        return asset(Storage::url($this->imagen));
    }

}
