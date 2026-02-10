<?php

namespace App\Http\Controllers;

use App\Models\Pregunta;
use Illuminate\Http\Request;

class PreguntaController extends Controller
{
    public function index(){

        $preguntas = Pregunta::orderBy('orden')->get();

        return view('preguntas.index', compact('preguntas'));
    }

    public function create(){
        return view('preguntas.create');
    }

    public function store(Request $request){

        $pregunta = Pregunta::create($request->except(['files']));

        
        if (!$request->show) {
            $pregunta->show = 0;
        }

        $pregunta->save();

        return redirect()->route('preguntas.index')->with('info','Pregunta agregada con éxito');
    }

    public function edit(Pregunta $pregunta){
        return view('preguntas.edit', compact('pregunta'));
    }

    public function update(Request $request, Pregunta $pregunta){

        $pregunta->update($request->except(['files']));

        if (!$request->show) {
            $pregunta->show = 0;
        }

        $pregunta->save();

        return redirect()->route('preguntas.index')->with('info','Pregunta actualizada con éxito');
    }

    
    public function destroy(Pregunta $pregunta){
        $pregunta->delete();
        return redirect()->route('preguntas.index');
    }
}
