<?php

namespace App\Http\Controllers;

use App\Models\Red;

use Illuminate\Http\Request;

class RedController extends Controller
{
    public function index(){

        $redes = Red::orderBy('orden')->get();

        return view('redes.index', compact('redes'));
    }

    public function create(){
        return view('redes.create');
    }

    public function store(Request $request){


        $red = Red::create($request->all());

        $red->save();

        return redirect()->route('redes.index')->with('info','Red social creada con éxito');
    }

    public function edit(Red $red){
        return view('redes.edit', compact('red'));
    }

    public function update(Request $request, Red $red){

        $red->update($request->all());

        if (!$request->show) {
            $red->show = 0;
        }
        
        $red->save();

        return redirect()->route('redes.index')->with('info','Red social actualizada con éxito');
    }

    public function destroy(Red $red){
        $red->delete();
        return redirect()->route('redes.index');
    }
}
