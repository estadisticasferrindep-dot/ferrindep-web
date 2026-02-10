<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TarifaLogistica;
use App\Models\MapeoZonaFlex;

class ShippingPricesController extends Controller
{
    public function index()
    {
        $tarifas = TarifaLogistica::orderBy('id')->get();
        // Agrupamos las zonas por tarifa para mostrarlas fácil
        $zonas = MapeoZonaFlex::orderBy('nombre_busqueda')->get()->groupBy('tarifa_id');

        return view('adm.shipping_prices.index', compact('tarifas', 'zonas'));
    }

    public function store(Request $request)
    {
        // 1. Guardar precios de tarifas
        if ($request->has('tarifas')) {
            foreach ($request->tarifas as $id => $monto) {
                TarifaLogistica::where('id', $id)->update(['monto' => $monto]);
            }
        }

        // 2. Agregar nueva zona (opcional)
        if ($request->has('new_zone_name') && $request->new_zone_name && $request->has('new_zone_tarifa')) {
            $nombre = mb_strtolower(trim($request->new_zone_name), 'UTF-8');
            // Chequear que no exista
            if (!MapeoZonaFlex::where('nombre_busqueda', $nombre)->exists()) {
                MapeoZonaFlex::create([
                    'nombre_busqueda' => $nombre,
                    'tarifa_id' => $request->new_zone_tarifa
                ]);
            }
        }

        // 3. Eliminar zonas checkeadas
        if ($request->has('delete_zones')) {
            MapeoZonaFlex::whereIn('id', $request->delete_zones)->delete();
        }

        return redirect()->back()->with('success', 'Configuración de Envíos actualizada correctamente.');
    }
}
