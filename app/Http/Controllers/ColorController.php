<?php

namespace App\Http\Controllers;


use App\Models\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    public function index(){

        $colores = Color::orderBy('orden')->get();

        return view('colores.index', compact('colores'));
    }

    public function create(){
        return view('colores.create');
    }

    public function store(Request $request){


        $color = Color::create($request->all());

        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen')->store('public/colores');
            $color->imagen = $imagen;
        }

        $color->save();

        return redirect()->route('colores.index')->with('info','Color agregada con éxito');
    }

    public function edit(Color $color){
        return view('colores.edit', compact('color'));
    }

    public function update(Request $request, Color $color){

        $color->update($request->all());

        if ($request->hasFile('imagen')) {
            $imagenRq = $request->file('imagen')->store('public/colores');
            $color->imagen = $imagenRq;
        }

        if (!$request->show) {
            $color->show = 0;
        }

        $color->save();

        return redirect()->route('colores.index')->with('info','Color actualizada con éxito');
    }

    public function destroy(Color $color){
        $color->delete();
        return redirect()->route('colores.index');
    }
}
