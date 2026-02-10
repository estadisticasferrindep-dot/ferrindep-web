<?php

namespace App\Models;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Galeria extends Model
{
    use HasFactory;
    
    
    protected $guarded = [];
    protected $appends = ['imagen_url'];
    
    public function getImagenUrlAttribute()
    {
        return asset(Storage::url($this->imagen));
    }
}