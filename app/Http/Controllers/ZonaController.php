<?php

namespace App\Http\Controllers;

use App\Models\Zona;
use Illuminate\Http\Request;

class ZonaController extends Controller
{
    public function index(){

        $zonas = Zona::orderBy('nombre')->get();

        return view('zonas.index', compact('zonas'));
    }

    public function create(){
        return view('zonas.create');
    }

    public function store(Request $request){

        $zona = Zona::create($request->except(['files']));


        $zona->save();

        return redirect()->route('zonas.index')->with('info','Zona agregada con éxito');
    }

    public function edit(Zona $zona){
        return view('zonas.edit', compact('zona'));
    }

    public function update(Request $request, Zona $zona){

        $zona->update($request->except(['files']));


        $zona->save();

        return redirect()->route('zonas.index')->with('info','Zona actualizada con éxito');
    }

    
    public function destroy(Zona $zona){
        $zona->delete();
        return redirect()->route('zonas.index');
    }
}
