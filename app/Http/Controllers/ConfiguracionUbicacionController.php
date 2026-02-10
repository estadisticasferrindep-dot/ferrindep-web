<?php

namespace App\Http\Controllers;

use App\Models\MapeoUbicacion;
use App\Models\Destino;
use Illuminate\Http\Request;

class ConfiguracionUbicacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mapeos = MapeoUbicacion::with('destino')->orderBy('id', 'desc')->get();
        return view('configuracion_ubicacion.index', compact('mapeos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $destinos = Destino::orderBy('nombre')->get();
        return view('configuracion_ubicacion.create', compact('destinos'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'ciudad_detectada' => 'required|string|max:255',
            'destino_id' => 'required|exists:destinos,id',
        ]);

        MapeoUbicacion::create($request->all());

        return redirect()->route('configuracion_ubicacion.index')
            ->with('info', 'Mapeo de ubicación creado con éxito');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MapeoUbicacion  $configuracion_ubicacion
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Note: Using $id and findOrFail because sometimes implicit binding names vary
        $mapeo = MapeoUbicacion::findOrFail($id);
        $destinos = Destino::orderBy('nombre')->get();
        return view('configuracion_ubicacion.edit', compact('mapeo', 'destinos'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MapeoUbicacion  $configuracion_ubicacion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $mapeo = MapeoUbicacion::findOrFail($id);

        $request->validate([
            'ciudad_detectada' => 'required|string|max:255',
            'destino_id' => 'required|exists:destinos,id',
        ]);

        $mapeo->update($request->all());

        return redirect()->route('configuracion_ubicacion.index')
            ->with('info', 'Mapeo de ubicación actualizado con éxito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MapeoUbicacion  $configuracion_ubicacion
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $mapeo = MapeoUbicacion::findOrFail($id);
        $mapeo->delete();

        return redirect()->route('configuracion_ubicacion.index')
            ->with('info', 'Mapeo eliminado correctamente');
    }
}
