<?php

namespace App\Http\Controllers;

use App\Models\Trabajo;
use App\Models\Seccion;
use App\Models\Galeria;

use Illuminate\Http\Request;

class TrabajoController extends Controller
{
    public function index(){

        $trabajos = Trabajo::orderBy('orden')->get();

        return view('show_trabajos.index', compact('trabajos'));
    }

    public function create_trabajo(){
        $relacionados = Trabajo::orderBy('orden')->get();


        return view('trabajos.create', compact('relacionados'));
    }

    public function store_trabajo(Request $request){

        $trabajo = Trabajo::create($request->all());

        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen')->store('public/imagenes');
            $trabajo->imagen = $imagen;
        }

        $trabajo->save();

        return redirect()->route('show_trabajos.index')->with('info','Trabajo creado con éxito');
    }

    public function edit_trabajo(Trabajo $trabajo){
        $relacionados = Trabajo::orderBy('orden')->get();

        return view('trabajos.edit', compact('trabajo','relacionados'));
    }

    public function update_trabajo(Request $request, Trabajo $trabajo){

        $trabajo->update($request->all());

        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen')->store('public/imagenes');
            $trabajo->imagen = $imagen;
        }


        if (!$request->show) {
            $trabajo->show = 0;
        }

        if (!$request->home) {
            $trabajo->home = 0;
        }

        $trabajo->save();

        return redirect()->route('show_trabajos.index')->with('info','Trabajo actualizado con éxito');
    }


    public function destroy_trabajo(Trabajo $trabajo){
        $trabajo->delete();
        return redirect()->route('show_trabajos.index');
    }




    public function create_seccion(Trabajo $trabajo){
        return view('secciones_p.create', compact('trabajo'));
    }

    public function store_seccion(Request $request, Trabajo $trabajo){

        $seccion = Seccion::create($request->all());

        $seccion->trabajo_id = $trabajo->id;
        
        $seccion->save();

        return redirect()->route('show_trabajos.index')->with('info','Sección agregada con éxito');
    }

    public function edit_seccion(Seccion $seccion){
        return view('secciones_p.edit', compact('seccion'));
    }

    public function update_seccion(Request $request, Seccion $seccion){

        $seccion->update($request->all());

        if (!$request->show) {
            $seccion->show = 0;
        }

        $seccion->save();

        return redirect()->route('show_trabajos.index')->with('info','Sección actualizada con éxito');
    }

    
    public function destroy_seccion(Seccion $seccion){
        $seccion->delete();
        return redirect()->route('show_trabajos.index');
    }




    public function create_galeria(Trabajo $trabajo){
        return view('galerias_p.create', compact('trabajo'));
    }

    public function store_galeria(Request $request, Trabajo $trabajo){

        $galeria = Galeria::create($request->all());

        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen')->store('public/imagenes');
            $galeria->imagen = $imagen;
        }

        $galeria->trabajo_id = $trabajo->id;
        
        $galeria->save();

        return redirect()->route('show_trabajos.index')->with('info','Archivo agregado con éxito');
    }

    public function edit_galeria(Galeria $galeria){
        return view('galerias_p.edit', compact('galeria'));
    }

    public function update_galeria(Request $request, Galeria $galeria){

        $galeria->update($request->all());

        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen')->store('public/imagenes');
            $galeria->imagen = $imagen;
        }

        if (!$request->show) {
            $galeria->show = 0;
        }

        $galeria->save();

        return redirect()->route('show_trabajos.index')->with('info','Archivo actualizado con éxito');
    }

    
    public function destroy_galeria(Galeria $galeria){
        $galeria->delete();
        return redirect()->route('show_trabajos.index');
    }
}