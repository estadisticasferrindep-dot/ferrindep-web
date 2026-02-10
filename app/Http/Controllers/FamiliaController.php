<?php

namespace App\Http\Controllers;


use App\Models\Familia;
use Illuminate\Http\Request;

class FamiliaController extends Controller
{
    public function index(){

        $familias = Familia::orderBy('orden')->get();

        return view('familias.index', compact('familias'));
    }

    public function create(){
        return view('familias.create');
    }

    public function store(Request $request){


        $familia = Familia::create($request->all());

        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen')->store('public/familias');
            $familia->imagen = $imagen;
        }

        $familia->save();

        return redirect()->route('familias.index')->with('info','Familia agregada con éxito');
    }

    public function edit(Familia $familia){
        return view('familias.edit', compact('familia'));
    }

    public function update(Request $request, Familia $familia){

        $familia->update($request->all());

        if ($request->hasFile('imagen')) {
            $imagenRq = $request->file('imagen')->store('public/familias');
            $familia->imagen = $imagenRq;
        }

        if (!$request->show) {
            $familia->show = 0;
        }

        $familia->save();

        return redirect()->route('familias.index')->with('info','Familia actualizada con éxito');
    }

    public function destroy(Familia $familia){
        $familia->delete();
        return redirect()->route('familias.index');
    }
}
