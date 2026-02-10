<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\Http\Request;

class ServicioController extends Controller
{
    public function index(){

        $servicios = Servicio::orderBy('orden')->get();

        return view('servicios.index', compact('servicios'));
    }

    public function create(){
        return view('servicios.create');
    }

    public function store(Request $request){


        $servicios = Servicio::create($request->all());

        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen')->store('public/servicios');
            $servicios->imagen = $imagen;
        }

        $servicios->save();

        return redirect()->route('servicios.index')->with('info','Servicio agregado con éxito');
    }

    public function edit(Servicio $servicio){
        return view('servicios.edit', compact('servicio'));
    }

    public function update(Request $request, Servicio $servicio){

        $servicio->update($request->all());

        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen')->store('public/servicios');
            $servicio->imagen = $imagen;
        }

        if (!$request->show) {
            $servicio->show = 0;
        }

        if (!$request->home) {
            $servicio->home = 0;
        }

        $servicio->save();

        return redirect()->route('servicios.index')->with('info','Servicio actualizado con éxito');
    }

    
    public function destroy(Servicio $servicio){
        $servicio->delete();
        return redirect()->route('servicios.index');
    }
}
