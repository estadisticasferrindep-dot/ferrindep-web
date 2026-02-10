<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index(){

        $usuarios = Usuario::orderBy('nombre')->get();

        return view('usuarios.index', compact('usuarios'));
    }

    public function create(){
        return view('usuarios.create');
    }

    public function store(Request $request){


        $usuarios = Usuario::create($request->all());

        $usuarios->save();

        return redirect()->route('usuarios.index')->with('info','Usuario agregado con éxito');
    }

    public function edit(Usuario $usuario){
        return view('usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, Usuario $usuario){

        $usuario->update($request->all());

        $usuario->save();

        return redirect()->route('usuarios.index')->with('info','Usuario actualizado con éxito');
    }

    
    public function destroy(Usuario $usuario){
        
        $usuario->delete();
        return redirect()->route('usuarios.index');
    }
}
