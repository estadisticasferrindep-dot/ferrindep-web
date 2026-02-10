<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    public function index(){

        $empresas = Empresa::get();

        return view('empresas.index', compact('empresas'));
    }


    public function update(Request $request, Empresa $empresa){

        $empresa->update($request->all());

        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen')->store('public/imagenes');
            $empresa->imagen = $imagen;
        }

        $empresa->save();

        return redirect()->route('empresas.index')->with('info','Información de la empresa actualizada con éxito');
    }
}