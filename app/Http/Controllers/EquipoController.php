<?php

namespace App\Http\Controllers;

use App\Models\Altura;
use App\Models\Clase;
use App\Models\Combustion;
use App\Models\Equipo;
use Illuminate\Http\Request;

class EquipoController extends Controller
{
    public function index(){

        $equipos = Equipo::orderBy('orden')->get();

        return view('equipos.index', compact('equipos'));
    }

    public function create(){

        $clases = Clase::orderBy('orden')->get();
        $alturas = Altura::orderBy('orden')->get();
        $combustiones = Combustion::orderBy('orden')->get();

        return view('equipos.create', compact('clases','alturas','combustiones'));
    }

    public function store(Request $request){

        $equipo = Equipo::create($request->all());

        if ($request->hasFile('imagen_general')) {
            $imagen_general = $request->file('imagen_general')->store('public/imagenes');
            $equipo->imagen_general = $imagen_general;
        }

        if ($request->hasFile('imagen_detalle')) {
            $imagen_detalle = $request->file('imagen_detalle')->store('public/imagenes');
            $equipo->imagen_detalle = $imagen_detalle;
        }

        if ($request->hasFile('ficha_tecnica')) {
            $ficha_tecnica = $request->file('ficha_tecnica')->store('public/fichas_tecnicas');
            $equipo->ficha_tecnica = $ficha_tecnica;
        }

        $equipo->save();

        return redirect()->route('equipos.index')->with('info','Equipo agregado con éxito');
    }

    public function edit(Equipo $equipo){
        
        $clases = Clase::orderBy('orden')->get();
        $alturas = Altura::orderBy('orden')->get();
        $combustiones = Combustion::orderBy('orden')->get();
        return view('equipos.edit', compact('equipo','clases','alturas','combustiones'));
    }

    public function update(Request $request, Equipo $equipo){

        $equipo->update($request->all());

        if ($request->hasFile('imagen_general')) {
            $imagen_general = $request->file('imagen_general')->store('public/imagenes');
            $equipo->imagen_general = $imagen_general;
        }

        if ($request->hasFile('imagen_detalle')) {
            $imagen_detalle = $request->file('imagen_detalle')->store('public/imagenes');
            $equipo->imagen_detalle = $imagen_detalle;
        }

        if ($request->hasFile('ficha_tecnica')) {
            $ficha_tecnica = $request->file('ficha_tecnica')->store('public/fichas_tecnicas');
            $equipo->ficha_tecnica = $ficha_tecnica;
        }


        if (!$request->show) {
            $equipo->show = 0;
        }

        if (!$request->en_venta) {
            $equipo->en_venta = 0;
        }

        if (!$request->en_alquiler) {
            $equipo->en_alquiler = 0;
        }

        $equipo->save();

        return redirect()->route('equipos.index')->with('info','Equipo actualizado con éxito');
    }

    
    public function destroy(Equipo $equipo){
        $equipo->delete();
        return redirect()->route('equipos.index');
    }
}