<?php

namespace App\Http\Controllers;

use App\Models\Zona;
use App\Models\Destino;
use Illuminate\Http\Request;

class DestinoController extends Controller
{
    public function index(){

        $destinos = Destino::orderBy('nombre')->get();

        return view('destinos.index', compact('destinos'));
    }

    public function create(){
        $zonas = Zona::orderBy('nombre')->get();
        return view('destinos.create', compact('zonas'));
    }

    public function store(Request $request){

        $destino = Destino::create($request->except(['files']));


        $destino->save();

        return redirect()->route('destinos.index')->with('info','Destino agregada con éxito');
    }

    public function edit(Destino $destino){
        $zonas = Zona::orderBy('nombre')->get();
        return view('destinos.edit', compact('destino', 'zonas'));
    }

    public function update(Request $request, Destino $destino){

        $destino->update($request->except(['files']));



        $destino->save();

        return redirect()->route('destinos.index')->with('info','Destino actualizada con éxito');
    }

    
    public function destroy(Destino $destino){
        $destino->delete();
        return redirect()->route('destinos.index');
    }
}
