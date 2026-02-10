<?php

namespace App\Http\Controllers;

use App\Models\Medida;
use Illuminate\Http\Request;

class MedidaController extends Controller
{
    public function index(){
        $medidas = Medida::orderBy('orden')->get();

        return view('medidas.index', compact('medidas'));
    }

    public function create(){
        return view('medidas.create');
    }

    public function store(Request $request){


        $medida = Medida::create($request->all());

        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen')->store('public/imagenes');
            $medida->imagen = $imagen;
        }

        $medida->save();

        return redirect()->route('medidas.index')->with('info','Categoría creada con éxito');
    }

    public function edit(Medida $medida){
        return view('medidas.edit', compact('medida'));
    }

    public function update(Request $request, Medida $medida){

        $medida->update($request->all());

        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen')->store('public/imagenes');
            $medida->imagen = $imagen;
        }
        

        $medida->save();

        return redirect()->route('medidas.index')->with('info','Categoría actualizada con éxito');
    }

    public function destroy(Medida $medida){
        $medida->delete();
        return redirect()->route('medidas.index');
    }
}