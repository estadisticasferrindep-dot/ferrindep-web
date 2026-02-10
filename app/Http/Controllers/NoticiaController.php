<?php

namespace App\Http\Controllers;

use App\Models\Claseblog;
use App\Models\Noticia;
use Illuminate\Http\Request;

class NoticiaController extends Controller
{
    public function index(){

        $noticias = Noticia::orderBy('orden')->get();

        return view('noticias.index', compact('noticias'));
    }

    public function create(){
        
        $claseblogs = Claseblog::orderBy('orden')->get();
        return view('noticias.create',compact('claseblogs'));
    }

    public function store(Request $request){


        $noticias = Noticia::create($request->all());

        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen')->store('public/noticias');
            $noticias->imagen = $imagen;
        }

        $noticias->save();

        return redirect()->route('noticias.index')->with('info','Noticia agregado con éxito');
    }

    public function edit(Noticia $noticia){
        $claseblogs = Claseblog::orderBy('orden')->get();

        return view('noticias.edit', compact('noticia','claseblogs'));
    }

    public function update(Request $request, Noticia $noticia){

        $noticia->update($request->all());

        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen')->store('public/noticias');
            $noticia->imagen = $imagen;
        }

        if (!$request->show) {
            $noticia->show = 0;
        }

        if (!$request->destacar) {
            $noticia->destacar = 0;
        }

        if (!$request->home) {
            $noticia->home = 0;
        }

        $noticia->save();

        return redirect()->route('noticias.index')->with('info','Noticia actualizado con éxito');
    }

    
    public function destroy(Noticia $noticia){
        $noticia->delete();
        return redirect()->route('noticias.index');
    }
}