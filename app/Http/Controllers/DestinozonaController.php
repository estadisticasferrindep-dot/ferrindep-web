<?php

namespace App\Http\Controllers;

use App\Models\Destinozona;
use Illuminate\Http\Request;
use App\Models\Zona;
use App\Models\Destino;


class DestinozonaController extends Controller
{
    public function index(){

        $destinozonas = Destinozona::get();

        return view('destinozonas.index', compact('destinozonas'));
    }

    public function create(){
        $zonas = Zona::orderBy('nombre')->get();
        $destinos = Destino::orderBy('nombre')->get();


        return view('destinozonas.create', compact('zonas','destinos'));
    }

    public function store(Request $request){

        $destinozona = Destinozona::create($request->except(['files']));


        $destinozona->save();

        return redirect()->route('destinozonas.index')->with('info','Destinozona agregada con éxito');
    }

    public function edit(Destinozona $destinozona){
        $zonas = Zona::orderBy('nombre')->get();
        $destinos = Destino::orderBy('nombre')->get();

        return view('destinozonas.edit', compact('destinozona', 'zonas','destinos'));
    }

    public function update(Request $request, Destinozona $destinozona){
        
        $destinozona->update($request->except(['files']));



        $destinozona->save();

        return redirect()->route('destinozonas.index')->with('info','Destinozona actualizada con éxito');
    }

    
    public function destroy(Destinozona $destinozona){
        $destinozona->delete();
        return redirect()->route('destinozonas.index');
    }
}
