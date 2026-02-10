<?php

namespace App\Http\Controllers;

use App\Models\Descargable;
use App\Models\Info;
use Illuminate\Http\Request;

class DescargaController extends Controller
{
    public function index(){

        $descargables = Descargable::orderBy('orden')->get();

        return view('descargas.index', compact('descargables'));
    }

    public function create_descargable(){
        return view('descargables.create');
    }

    public function store_descargable(Request $request){

        $descargable = Descargable::create($request->all());

        $descargable->save();

        return redirect()->route('descargas.index')->with('info','Descargable creado con éxito');
    }

    public function edit_descargable(Descargable $descargable){
        return view('descargables.edit', compact('descargable'));
    }

    public function update_descargable(Request $request, Descargable $descargable){

        $descargable->update($request->all());

        if (!$request->show) {
            $descargable->show = 0;
        }

        $descargable->save();

        return redirect()->route('descargas.index')->with('info','Descargable actualizado con éxito');
    }


    public function destroy_descargable(Descargable $descargable){
        $descargable->delete();
        return redirect()->route('descargas.index');
    }




    public function create_info(Descargable $descargable){
        return view('infos.create', compact('descargable'));
    }

    public function store_info(Request $request, Descargable $descargable){

        $info = Info::create($request->all());

        if ($request->hasFile('descarga')) {
            $descarga = $request->file('descarga')->store('public/descargas');
            $info->descarga = $descarga;
        }

        $info->descargable_id = $descargable->id;

        $info->save();

        return redirect()->route('descargas.index')->with('info','Archivo agregado con éxito');
    }

    public function edit_info(Info $info){
        return view('infos.edit', compact('info'));
    }

    public function update_info(Request $request, Info $info){

        $info->update($request->all());

        if ($request->hasFile('descarga')) {
            $descarga = $request->file('descarga')->store('public/descargas');
            $info->descarga = $descarga;
        }

        if (!$request->show) {
            $info->show = 0;
        }

        $info->save();

        return redirect()->route('descargas.index')->with('info','Archivo actualizado con éxito');
    }

    
    public function destroy_info(Info $info){
        $info->delete();
        return redirect()->route('descargas.index');
    }
}