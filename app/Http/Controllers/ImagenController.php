<?php

namespace App\Http\Controllers;


use App\Models\Imagen;
use Illuminate\Http\Request;

class ImagenController extends Controller
{
    public function index(){

        $imagenes = Imagen::orderBy('orden')->get();

        return view('imagenes.index', compact('imagenes'));
    }

    public function create(){
        return view('imagenes.create');
    }

    public function store(Request $request){


        $imagen = Imagen::create($request->all());

        if ($request->hasFile('imagen')) {
            $imagenRq = $request->file('imagen')->store('public/imagenes');
            $imagen->imagen = $imagenRq;
        }

        $imagen->save();

        return redirect()->route('imagenes.index')->with('info','Imagen agregada con éxito');
    }

    public function edit(Imagen $imagen){
        return view('imagenes.edit', compact('imagen'));
    }

    public function update(Request $request, Imagen $imagen){

        $imagen->update($request->all());

        if ($request->hasFile('imagen')) {
            $imagenRq = $request->file('imagen')->store('public/imagenes');
            $imagen->imagen = $imagenRq;
        }

        if (!$request->show) {
            $imagen->show = 0;
        }

        $imagen->save();

        return redirect()->route('imagenes.index')->with('info','Imagen actualizada con éxito');
    }

    public function destroy(Imagen $imagen){
        $imagen->delete();
        return redirect()->route('imagenes.index');
    }
}
