<?php

namespace App\Http\Controllers;

use App\Models\Envio;
use Illuminate\Http\Request;

class EnvioController extends Controller
{
    public function index(){
        $envios = Envio::get();

        return view('envios.index', compact('envios'));
    }

    public function create(){
        return view('envios.create');
    }

    public function store(Request $request){


        $envio = Envio::create($request->all());

        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen')->store('public/imagenes');
            $envio->imagen = $imagen;
        }

        $envio->save();

        return redirect()->route('envios.index')->with('info','Categoría creada con éxito');
    }

    public function edit(Envio $envio){
        return view('envios.edit', compact('envio'));
    }

    public function update(Request $request, Envio $envio){

        $envio->update($request->all());

        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen')->store('public/imagenes');
            $envio->imagen = $imagen;
        }
        

        $envio->save();

        return redirect()->route('envios.index')->with('info','Categoría actualizada con éxito');
    }

    public function destroy(Envio $envio){
        $envio->delete();
        return redirect()->route('envios.index');
    }
}