<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Medida;
use App\Models\Espesor;
use App\Models\Color;
use App\Models\Diametro;
use App\Models\Rango;
use App\Models\Presentacion;
use App\Models\ColorProducto;
use App\Models\Familia;
use App\Models\Galeria;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    /* =======================
       LISTADO (con filtro y orden)
       ======================= */
    public function index(Request $request)
    {
        // 1) Tomo los parámetros tal cual llegan (null o string)
        $mostrar = $request->input('mostrar');     // '1', '0' o null
        $anchoId = $request->input('ancho');       // id o ''
        $medidaId = $request->input('medida_id');   // id o ''
        $espesorId = $request->input('espesor_id');  // id o ''
        $q = trim($request->input('q', ''));

        // 2) Query base con joins (usá tus propios joins/columns si ya los tenés)
        $qProd = Producto::query()
            ->leftJoin('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->leftJoin('medidas', 'medidas.id', '=', 'productos.medida_id')
            ->leftJoin('espesores', 'espesores.id', '=', 'productos.espesor_id')
            ->select('productos.*');

        // 3) Filtros INDEPENDIENTES
        $qProd->when($request->filled('mostrar'), function ($q) use ($mostrar) {
            $q->where('productos.show', $mostrar === '1' ? 1 : 0);
        });

        $qProd->when($request->filled('ancho'), function ($q) use ($anchoId) {
            $q->where('productos.categoria_id', $anchoId);
        });

        $qProd->when($request->filled('medida_id'), function ($q) use ($medidaId) {
            $q->where('productos.medida_id', $medidaId);
        });

        $qProd->when($request->filled('espesor_id'), function ($q) use ($espesorId) {
            $q->where('productos.espesor_id', $espesorId);
        });

        // 4) Buscar "a prueba de balas" (10x10 == "10 x 10", "30cm" == "30 cm", etc.)
        if ($q !== '') {
            $needle = mb_strtolower($q, 'UTF-8');
            $needle = str_replace([' ', '.', ','], '', $needle);
            $param = "%{$needle}%";

            $qProd->where(function ($w) use ($param) {
                $w->orWhereRaw("LOWER(REPLACE(REPLACE(REPLACE(medidas.medidas,' ',''),'.',''),',','')) LIKE ?", [$param])
                    ->orWhereRaw("LOWER(REPLACE(REPLACE(REPLACE(categorias.nombre,' ',''),'.',''),',','')) LIKE ?", [$param])
                    ->orWhereRaw("LOWER(REPLACE(REPLACE(REPLACE(espesores.espesor,' ',''),'.',''),',','')) LIKE ?", [$param])
                    ->orWhereRaw("LOWER(REPLACE(REPLACE(REPLACE(productos.nombre,' ',''),'.',''),',','')) LIKE ?", [$param]);
            });
        }

        // 5) Orden (primero ancho numérico, después medida)
        $qProd->orderByRaw('CAST(categorias.nombre AS UNSIGNED) ASC')
            ->orderBy('medidas.medidas', 'ASC');

        // 6) Paginar conservando filtros en la URL
        $productos = $qProd->get();

        // 7) Listas de filtros con SOLO valores que existen en productos
        $anchos = Categoria::join('productos', 'productos.categoria_id', '=', 'categorias.id')
            ->select('categorias.id', 'categorias.nombre')
            ->distinct()
            ->orderByRaw('CAST(categorias.nombre AS UNSIGNED) ASC')
            ->pluck('categorias.nombre', 'categorias.id');

        $medidas = Medida::join('productos', 'productos.medida_id', '=', 'medidas.id')
            ->select('medidas.id', 'medidas.medidas')
            ->distinct()
            ->orderBy('medidas.medidas', 'ASC')
            ->pluck('medidas.medidas', 'medidas.id');

        $espesores = Espesor::join('productos', 'productos.espesor_id', '=', 'espesores.id')
            ->select('espesores.id', 'espesores.espesor')
            ->distinct()
            ->orderBy('espesores.espesor', 'ASC')
            ->pluck('espesores.espesor', 'espesores.id');

        return view('show_productos.index', compact('productos', 'anchos', 'medidas', 'espesores'));
    }



    /* =======================
       CRUD de Producto
       ======================= */
    public function create_producto()
    {
        $categorias = Categoria::orderBy('orden')->get();
        $medidas = Medida::orderBy('medidas')->get();
        $espesores = Espesor::orderBy('espesor')->get();
        $familias = Familia::orderBy('orden')->get();
        $relacionados = Producto::orderBy('orden')->get();

        return view('productos.create', compact('categorias', 'medidas', 'espesores', 'relacionados', 'familias'));
    }

    public function store_producto(Request $request)
    {
        $producto = Producto::create($request->all());

        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen')->store('public/imagenes');
            $producto->imagen = $imagen;
        }

        $producto->save();
        return redirect()->route('show_productos.index')->with('info', 'Producto creado con éxito');
    }

    public function edit_producto(Producto $producto)
    {
        $categorias = Categoria::orderBy('orden')->get();
        $medidas = Medida::orderBy('medidas')->get();
        $espesores = Espesor::orderBy('espesor')->get();
        $familias = Familia::orderBy('orden')->get();
        $relacionados = Producto::orderBy('orden')->get();

        return view('productos.edit', compact('producto', 'categorias', 'medidas', 'espesores', 'relacionados', 'familias'));
    }

    public function update_producto(Request $request, Producto $producto)
    {
        $producto->update($request->all());

        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen')->store('public/imagenes');
            $producto->imagen = $imagen;
        }

        // Normalizar checkboxes
        if (!$request->show)
            $producto->show = 0;
        if (!$request->destacado)
            $producto->destacado = 0;
        if (!$request->con_nombre)
            $producto->con_nombre = 0;
        if (!$request->oferta)
            $producto->oferta = 0;
        if (!$request->anular_envio)
            $producto->anular_envio = 0;

        $producto->save();
        return redirect()->route('show_productos.index')->with('info', 'Producto actualizado con éxito');
    }

    public function destroy_producto(Producto $producto)
    {
        $producto->delete();
        return redirect()->route('show_productos.index');
    }

    /* =======================
       Colores
       ======================= */
    public function create_color(Producto $producto)
    {
        $colores = Color::orderBy('orden')->get();
        return view('colores_p.create', compact('producto', 'colores'));
    }

    public function store_color(Request $request, Producto $producto)
    {
        $color = ColorProducto::create($request->all());
        $color->producto_id = $producto->id;
        $color->save();

        return redirect()->route('productos.edit', $producto->id)->with('info', 'Color agregado con éxito');
    }

    public function edit_color(ColorProducto $cp, Producto $producto)
    {
        $colores = Color::orderBy('orden')->get();
        return view('colores_p.edit', compact('cp', 'colores', 'producto'));
    }

    public function update_color(Request $request, ColorProducto $cp, Producto $producto)
    {
        $cp->update($request->all());
        if (!$request->show)
            $cp->show = 0;
        $cp->save();

        return redirect()->route('productos.edit', $producto->id)->with('info', 'Sección actualizada con éxito');
    }

    public function destroy_color(ColorProducto $cp, Producto $producto)
    {
        $cp->delete();
        return redirect()->route('productos.edit', $producto->id);
    }

    /* =======================
       Galería
       ======================= */
    public function create_galeria(Producto $producto)
    {
        return view('galerias_p.create', compact('producto'));
    }

    public function store_galeria(Request $request, Producto $producto)
    {
        $galeria = Galeria::create($request->all());

        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen')->store('public/imagenes');
            $galeria->imagen = $imagen;
        }

        $galeria->producto_id = $producto->id;
        $galeria->save();

        return redirect()->route('show_productos.index')->with('info', 'Archivo agregado con éxito');
    }

    public function edit_galeria(Galeria $galeria)
    {
        return view('galerias_p.edit', compact('galeria'));
    }

    public function update_galeria(Request $request, Galeria $galeria)
    {
        $galeria->update($request->all());

        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen')->store('public/imagenes');
            $galeria->imagen = $imagen;
        }

        if (!$request->show)
            $galeria->show = 0;

        $galeria->save();
        return redirect()->route('show_productos.index')->with('info', 'Archivo actualizado con éxito');
    }

    public function destroy_galeria(Galeria $galeria)
    {
        $galeria->delete();
        return redirect()->route('show_productos.index');
    }

    /* =======================
       Diámetros
       ======================= */
    public function create_diametro(Producto $producto)
    {
        return view('diametros_p.create', compact('producto'));
    }

    public function store_diametro(Request $request, Producto $producto)
    {
        $diametro = Diametro::create($request->all());
        $diametro->producto_id = $producto->id;
        $diametro->save();

        return redirect()->route('productos.edit', $producto->id)->with('info', 'Diámetro agregado con éxito');
    }

    public function edit_diametro(Diametro $diametro, Producto $producto)
    {
        return view('diametros_p.edit', compact('diametro', 'producto'));
    }

    public function update_diametro(Request $request, Diametro $diametro, Producto $producto)
    {
        $diametro->update($request->all());
        if (!$request->show)
            $diametro->show = 0;
        $diametro->save();

        return redirect()->route('productos.edit', $producto->id)->with('info', 'Diámetro actualizado con éxito');
    }

    public function destroy_diametro(Diametro $diametro, Producto $producto)
    {
        $diametro->delete();
        return redirect()->route('productos.edit', $producto->id);
    }

    /* =======================
       Rangos
       ======================= */
    public function create_rango(Producto $producto)
    {
        return view('rangos_p.create', compact('producto'));
    }

    public function store_rango(Request $request, Producto $producto)
    {
        $rango = Rango::create($request->all());
        $rango->producto_id = $producto->id;
        $rango->save();

        return redirect()->route('productos.edit', $producto->id)->with('info', 'Diámetro agregado con éxito');
    }

    public function edit_rango(Rango $rango, Producto $producto)
    {
        return view('rangos_p.edit', compact('rango', 'producto'));
    }

    public function update_rango(Request $request, Rango $rango, Producto $producto)
    {
        $rango->update($request->all());
        if (!$request->ultimo)
            $rango->ultimo = 0;
        $rango->save();

        return redirect()->route('productos.edit', $producto->id)->with('info', 'Diámetro actualizado con éxito');
    }

    public function destroy_rango(Rango $rango, Producto $producto)
    {
        $rango->delete();
        return redirect()->route('productos.edit', $producto->id);
    }

    /* =======================
       Presentaciones
       ======================= */
    public function create_presentacion(Producto $producto)
    {
        return view('presentaciones_p.create', compact('producto'));
    }

    public function store_presentacion(Request $request, Producto $producto)
    {
        $presentacion = Presentacion::create($request->all());
        $presentacion->producto_id = $producto->id;
        $presentacion->save();

        return redirect()->route('productos.edit', $producto->id)->with('info', 'Diámetro agregado con éxito');
    }

    public function edit_presentacion(Presentacion $presentacion, Producto $producto)
    {
        return view('presentaciones_p.edit', compact('presentacion', 'producto'));
    }

    public function update_presentacion(Request $request, Presentacion $presentacion, Producto $producto)
    {
        $presentacion->update($request->all());
        if (!$request->show)
            $presentacion->show = 0;
        if (!$request->free)
            $presentacion->free = 0;
        if (!$request->envio_gratis_flex)
            $presentacion->envio_gratis_flex = 0;

        // NUEVO: Flags de Envío Gratis por Zona (1-4)
        if (!$request->envio_gratis_zona_1)
            $presentacion->envio_gratis_zona_1 = 0;
        if (!$request->envio_gratis_zona_2)
            $presentacion->envio_gratis_zona_2 = 0;
        if (!$request->envio_gratis_zona_3)
            $presentacion->envio_gratis_zona_3 = 0;
        if (!$request->envio_gratis_zona_4)
            $presentacion->envio_gratis_zona_4 = 0;
        $presentacion->save();

        return redirect()->route('productos.edit', $producto->id)->with('info', 'Diámetro actualizado con éxito');
    }

    public function update_presentacion_2(Request $request, Presentacion $presentacion, Producto $producto)
    {
        $presentacion->update($request->all());
        $presentacion->save();

        return redirect()->route('productos.edit', $producto->id)->with('info', 'Diámetro actualizado con éxito');
    }

    public function destroy_presentacion(Presentacion $presentacion, Producto $producto)
    {
        $presentacion->delete();
        return redirect()->route('productos.edit', $producto->id);
    }
}