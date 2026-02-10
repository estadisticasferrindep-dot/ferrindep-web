<?php

namespace App\Http\Controllers;

use App\Models\Direccion;
use Illuminate\Http\Request;

class DireccionController extends Controller
{
    public function index(){

        $direcciones = Direccion::orderBy('orden')->get();

        return view('direcciones.index', compact('direcciones'));
    }

    public function create(){
        return view('direcciones.create');
    }

    public function store(Request $request){

        $direccion = Direccion::create($request->except(['files']));

        $direccion->save();

        return redirect()->route('direcciones.index')->with('info','Dirección creada con éxito');
    }

    public function edit(Direccion $direccion){
        return view('direcciones.edit', compact('direccion'));
    }

    public function update(Request $request, Direccion $direccion){

        $direccion->update($request->all());

        if (!$request->show) {
            $direccion->show = 0;
        }
        
        if (!$request->footer) {
            $direccion->footer = 0;
        }

        $direccion->save();

        return redirect()->route('direcciones.index')->with('info','Dirección actualizada con éxito');
    }

    public function destroy(Direccion $direccion){
        $direccion->delete();
        return redirect()->route('direcciones.index');
    }
}
