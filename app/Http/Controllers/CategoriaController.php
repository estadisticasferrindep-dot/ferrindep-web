<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index(){
        $categorias = Categoria::orderBy('orden')->get();

        return view('categorias.index', compact('categorias'));
    }

    public function create(){
        return view('categorias.create');
    }

    public function store(Request $request){


        $categoria = Categoria::create($request->all());

        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen')->store('public/imagenes');
            $categoria->imagen = $imagen;
        }

        $categoria->save();

        return redirect()->route('categorias.index')->with('info','Categoría creada con éxito');
    }

    public function edit(Categoria $categoria){
        return view('categorias.edit', compact('categoria'));
    }

    public function update(Request $request, Categoria $categoria){

        $categoria->update($request->all());

        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen')->store('public/imagenes');
            $categoria->imagen = $imagen;
        }
        
        if (!$request->show) {
            $categoria->show = 0;
        }
        
        if (!$request->con_nombre) {
            $categoria->con_nombre = 0;
        }
        $categoria->save();

        return redirect()->route('categorias.index')->with('info','Categoría actualizada con éxito');
    }

    public function destroy(Categoria $categoria){
        $categoria->delete();
        return redirect()->route('categorias.index');
    }
}