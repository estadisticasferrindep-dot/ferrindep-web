<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Configpedido;

class ConfigpedidoController extends Controller
{
    public function index(){

        $configpedidos = Configpedido::get();
        
        return view('configpedidos.index', compact('configpedidos'));
    }


    public function update(Request $request, Configpedido $configpedido){

        $configpedido->update($request->all());

        return redirect()->route('configpedidos.index')->with('info','Configuración actualizada con éxito');
    }
}
