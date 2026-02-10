<?php

namespace App\Http\Controllers;

use App\Models\Espesor;
use Illuminate\Http\Request;

class EspesorController extends Controller
{
    public function index(){
        $espesores = Espesor::orderBy('orden')->get();

        return view('espesores.index', compact('espesores'));
    }

    public function create(){
        return view('espesores.create');
    }

    public function store(Request $request){


        $espesor = Espesor::create($request->all());

        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen')->store('public/imagenes');
            $espesor->imagen = $imagen;
        }

        $espesor->save();

        return redirect()->route('espesores.index')->with('info','Categoría creada con éxito');
    }

    public function edit(Espesor $espesor){
        return view('espesores.edit', compact('espesor'));
    }

    public function update(Request $request, Espesor $espesor){

        $espesor->update($request->all());

        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen')->store('public/imagenes');
            $espesor->imagen = $imagen;
        }
        

        $espesor->save();

        return redirect()->route('espesores.index')->with('info','Categoría actualizada con éxito');
    }

    public function destroy(Espesor $espesor){
        $espesor->delete();
        return redirect()->route('espesores.index');
    }
}