<?php

namespace App\Http\Controllers;

use App\Models\Pesozona;
use Illuminate\Http\Request;
use App\Models\Zona;


class PesozonaController extends Controller
{
    public function index(){

        $pesozonas = Pesozona::orderBy('peso')->get();

        return view('pesozonas.index', compact('pesozonas'));
    }

    public function create(){
        $zonas = Zona::orderBy('nombre')->get();
        
        return view('pesozonas.create', compact('zonas'));
    }

    public function store(Request $request){

        $pesozona = Pesozona::create($request->except(['files']));
        $pesozona->save();

        return redirect()->route('pesozonas.index')->with('info','Pesozona agregada con éxito');
    }

    public function edit(Pesozona $pesozona){
        $zonas = Zona::orderBy('nombre')->get();

        return view('pesozonas.edit', compact('zonas', 'pesozona'));
    }

    public function update(Request $request, Pesozona $pesozona){

        $pesozona->update($request->except(['files']));


        $pesozona->save();

        return redirect()->route('pesozonas.index')->with('info','Pesozona actualizada con éxito');
    }

    
    public function destroy(Pesozona $pesozona){
        $pesozona->delete();
        return redirect()->route('pesozonas.index');
    }
}
