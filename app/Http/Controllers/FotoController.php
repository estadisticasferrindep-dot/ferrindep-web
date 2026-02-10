<?php

namespace App\Http\Controllers;


use App\Models\Foto;
use Illuminate\Http\Request;

class FotoController extends Controller
{
    public function index(){

        $fotos = Foto::orderBy('orden')->get();

        return view('fotos.index', compact('fotos'));
    }

    public function create(){
        return view('fotos.create');
    }

    public function store(Request $request){


        $foto = Foto::create($request->all());

        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen')->store('public/fotos');
            $foto->imagen = $imagen;
        }

        $foto->save();

        return redirect()->route('fotos.index')->with('info','Foto agregada con éxito');
    }

    public function edit(Foto $foto){
        return view('fotos.edit', compact('foto'));
    }

    public function update(Request $request, Foto $foto){

        $foto->update($request->all());

        if ($request->hasFile('imagen')) {
            $imagenRq = $request->file('imagen')->store('public/fotos');
            $foto->imagen = $imagenRq;
        }

        if (!$request->show) {
            $foto->show = 0;
        }

        $foto->save();

        return redirect()->route('fotos.index')->with('info','Foto actualizada con éxito');
    }

    public function destroy(Foto $foto){
        $foto->delete();
        return redirect()->route('fotos.index');
    }
}
