<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(){

        $clientes = Cliente::orderBy('orden')->get();

        return view('clientes.index', compact('clientes'));
    }

    public function create(){
        return view('clientes.create');
    }

    public function store(Request $request){


        $cliente = Cliente::create($request->all());

        if ($request->hasFile('imagen')) {
            $clienteRq = $request->file('imagen')->store('public/clientes');
            $cliente->imagen = $clienteRq;
        }

        $cliente->save();

        return redirect()->route('clientes.index')->with('info','Cliente creado con éxito');
    }

    public function edit(Cliente $cliente){
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente){

        $cliente->update($request->all());

        if ($request->hasFile('imagen')) {
            $clienteRq = $request->file('imagen')->store('public/clientes');
            $cliente->imagen = $clienteRq;
        }

        if (!$request->show) {
            $cliente->show = 0;
        }

        $cliente->save();

        return redirect()->route('clientes.index')->with('info','Cliente actualizado con éxito');
    }

    
    public function destroy(Cliente $cliente){
        $cliente->delete();
        return redirect()->route('clientes.index');
    }
}
