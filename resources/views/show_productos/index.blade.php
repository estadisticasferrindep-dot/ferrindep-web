@extends('layouts.app')

@section('title','Productos')

@section('content')
<div class="container cont-descargas">
  <div class="row justify-content-center">
    <div class="col-md-10">

      @if (session('status'))
        <div class="alert alert-success" role="alert">
          {{ session('status') }}
        </div>
      @endif

      {{-- Botón AÑADIR arriba a la derecha --}}
      <a style="color:white;" href="{{ route('productos.create') }}">
        <button type="button" class="btn btn-success" style="float:right;">
          <i class="fas fa-plus" style="color:white; margin-right:7px;"></i>AÑADIR
        </button>
      </a>
      <br>

      {{-- ====== FILTROS ====== --}}
<form id="filtrosForm" method="GET" action="{{ route('show_productos.index') }}" class="row g-2 align-items-end mb-3">

  <div class="col-auto">
    <label for="f-mostrar" class="form-label" style="font-weight:600; color:#03224e;">Mostrar</label>
    <select id="f-mostrar" name="mostrar" class="form-select" form="filtrosForm">
      <option value=""  {{ request('mostrar')==='' ? 'selected' : '' }}>Todos</option>
      <option value="1" {{ request('mostrar')==='1' ? 'selected' : '' }}>Mostrar</option>
      <option value="0" {{ request('mostrar')==='0' ? 'selected' : '' }}>No mostrar</option>
    </select>
  </div>

  <div class="col-auto">
    <label for="f-ancho" class="form-label" style="font-weight:600; color:#03224e;">Ancho</label>
    <select id="f-ancho" name="ancho" class="form-select" form="filtrosForm">
      <option value="">Todos</option>
      @foreach($anchos as $id => $nombre)
        <option value="{{ $id }}" {{ (string)request('ancho')===(string)$id ? 'selected' : '' }}>
          {{ $nombre }}@if(is_numeric($nombre)) cm @endif
        </option>
      @endforeach
    </select>
  </div>

  <div class="col-auto">
    <label for="f-medida" class="form-label" style="font-weight:600; color:#03224e;">Medida</label>
    <select id="f-medida" name="medida_id" class="form-select" form="filtrosForm">
      <option value="">Todas</option>
      @foreach($medidas as $id => $texto)
        <option value="{{ $id }}" {{ (string)request('medida_id')===(string)$id ? 'selected' : '' }}>
          {{ $texto }}
        </option>
      @endforeach
    </select>
  </div>

  <div class="col-auto">
    <label for="f-espesor" class="form-label" style="font-weight:600; color:#03224e;">Espesor</label>
    <select id="f-espesor" name="espesor_id" class="form-select" form="filtrosForm">
      <option value="">Todos</option>
      @foreach($espesores as $id => $texto)
        <option value="{{ $id }}" {{ (string)request('espesor_id')===(string)$id ? 'selected' : '' }}>
          {{ $texto }}
        </option>
      @endforeach
    </select>
  </div>

  <div class="col-auto">
    <label for="f-q" class="form-label" style="font-weight:600; color:#03224e;">Buscar</label>
    <input id="f-q" name="q" type="text" class="form-control" form="filtrosForm"
           placeholder="ej: 10x10, 30 cm, 1 mm" value="{{ request('q') }}">
  </div>

  <div class="col-auto">
    <button type="submit" class="btn btn-primary" form="filtrosForm">Filtrar</button>
    <a href="{{ route('show_productos.index') }}" class="btn btn-outline-secondary">Limpiar</a>
  </div>

</form>
{{-- ====== /FILTROS ====== --}}


      @foreach ($productos as $producto)
        <br>
        <div class="card" style="margin-top:15px;">
          <div class="card-body p-0">

            <div style="padding-top:15px;">
              <div class="container">
                <div class="row align-items-center">

                  {{-- Título del producto --}}
                  @if($producto->con_nombre)
                    <h4 class="col-4" style="color:#03224e; font-size:24px; margin-bottom:15px; margin-left:5px;">
                      {{ $producto->nombre }}
                    </h4>
                  @else
                    <h4 class="col-4" style="color:#03224e; font-size:24px; margin-bottom:15px; margin-left:5px;">
                      {{ $producto->medidas->medidas }} / {{ $producto->categoria->nombre }} / {{ $producto->espesor->espesor }}
                    </h4>
                  @endif

                  {{-- Miniatura del producto (usa thumb si existe; si no, original) --}}
                  @php
                    $pImg    = $producto->imagen;
                    $pThumb1 = preg_replace('/\/([^\/]+)$/', '/thumbs/$1', $pImg);     // carpeta /thumbs
                    $pThumb2 = preg_replace('/(\.[^.]+)$/', '-thumb$1', $pImg);       // sufijo -thumb.ext
                    $pThumb3 = preg_replace('/(\.[^.]+)$/', '-thumb.webp', $pImg);    // sufijo -thumb.webp
                    $pPath   = Storage::exists($pThumb1) ? $pThumb1 :
                               (Storage::exists($pThumb2) ? $pThumb2 :
                               (Storage::exists($pThumb3) ? $pThumb3 : $pImg));
                    $pSrc    = asset(Storage::url($pPath));
                  @endphp
                  <div class="col-2">
                    <img src="{{ $pSrc }}" alt="Foto producto"
                         loading="lazy" decoding="async" fetchpriority="low"
                         width="90" height="60"
                         style="width:90px; height:60px; object-fit:cover; border-radius:4px;">
                  </div>

                  <div class="col-2">
                    {{ $producto->show ? 'Mostrar' : 'No mostrar' }}
                  </div>

                  {{-- Acciones --}}
                  <div class="col-3" style="display:flex;">
                    <a style="color:white;" href="{{ route('galeriasP.create', $producto) }}">
                      <button type="button" class="btn btn-success" style="margin-right:5px; margin-bottom:10px">
                        <span>AÑADIR FOTO</span>
                      </button>
                    </a>

                    <a style="color:white;" href="{{ route('productos.edit', $producto) }}">
                      <button type="button" class="btn btn-primary" style="margin-right:5px; margin-bottom:10px">
                        <i class="far fa-edit"></i>
                      </button>
                    </a>

                    <form action="{{ route('productos.destroy', $producto) }}" method="POST">
                      @csrf
                      @method('DELETE')
                      <button type="submit" onclick="return confirm('¿Estas seguro?');" class="btn btn-danger">
                        <span style="color:white;"><i class="fas fa-trash-alt"></i></span>
                      </button>
                    </form>
                  </div>
                </div>
              </div>
            </div>

            {{-- Tabla de galería de imágenes --}}
            <table class="table" style="width:100%; table-layout:fixed;">
              <thead style="color:#03224e">
                <tr>
                  <th scope="col" style="width:120px;">Fotos</th>
                  <th scope="col" style="width:120px;">Mostrar</th>
                  <th scope="col" style="width:120px;">Orden</th>
                  <th scope="col">Acciones</th>
                </tr>
              </thead>

              <tbody>
                @if ($producto->galerias)
                  @foreach ($producto->galerias as $galeria)
                    @php
                      $gImg    = $galeria->imagen;
                      $gThumb1 = preg_replace('/\/([^\/]+)$/', '/thumbs/$1', $gImg);
                      $gThumb2 = preg_replace('/(\.[^.]+)$/', '-thumb$1', $gImg);
                      $gThumb3 = preg_replace('/(\.[^.]+)$/', '-thumb.webp', $gImg);
                      $gPath   = Storage::exists($gThumb1) ? $gThumb1 :
                                 (Storage::exists($gThumb2) ? $gThumb2 :
                                 (Storage::exists($gThumb3) ? $gThumb3 : $gImg));
                      $gSrc    = asset(Storage::url($gPath));
                    @endphp
                    <tr>
                      <td style="vertical-align:middle;">
                        <img src="{{ $gSrc }}" alt="Foto"
                             loading="lazy" decoding="async" fetchpriority="low"
                             width="90" height="60"
                             style="width:90px; height:60px; object-fit:cover; border-radius:4px;">
                      </td>
                      <td style="vertical-align:middle;">{{ $galeria->show ? 'Si' : 'No' }}</td>
                      <td style="vertical-align:middle;">{{ $galeria->orden }}</td>
                      <td>
                        <div style="display:flex; align-items:center">
                          <a style="color:white;" href="{{ route('galeriasP.edit', $galeria) }}">
                            <button type="button" class="btn btn-primary" style="margin-right:5px;">
                              <i class="far fa-edit"></i>
                            </button>
                          </a>
                          <form action="{{ route('galeriasP.destroy', $galeria) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('¿Estas seguro?');" class="btn btn-danger">
                              <span style="color:white;"><i class="fas fa-trash-alt"></i></span>
                            </button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                @endif
              </tbody>
            </table>

            <hr style="font-weight:bold; color:black">
          </div>
        </div>
      @endforeach

      {{-- Paginación --}}
      @if($productos instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="mt-3 d-flex justify-content-center">
          {{ $productos->withQueryString()->links() }}
        </div>
      @endif

    </div> {{-- /.col-md-10 --}}
  </div>   {{-- /.row --}}
</div>     {{-- /.container --}}
@endsection
