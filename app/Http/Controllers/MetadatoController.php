<?php

namespace App\Http\Controllers;

use App\Models\Metadato;

use Illuminate\Http\Request;

class MetadatoController extends Controller
{
    public function index(){

        $metadatos = Metadato::get();

        return view('metadatos.index', compact('metadatos'));
    }

    public function create(){
        return view('metadatos.create');
    }

    public function store(Request $request){


        $metadato = Metadato::create($request->all());

        $metadato->save();

        return redirect()->route('metadatos.index')->with('info','Metadato creado con éxito');
    }

    public function edit(Metadato $metadato){
        return view('metadatos.edit', compact('metadato'));
    }

    public function update(Request $request, Metadato $metadato){

        $metadato->update($request->all());

        $metadato->save();

        return redirect()->route('metadatos.index')->with('info','Metadato actualizado con éxito');
    }

    public function destroy(Metadato $metadato){
        $metadato->delete();
        return redirect()->route('metadatos.index');
    }
}
