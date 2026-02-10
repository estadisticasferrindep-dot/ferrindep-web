{{-- resources/views/web/buscar.blade.php --}}
<div class="container" style="max-width:900px;margin:20px auto;padding:0 12px">
  <form action="{{ route('buscar') }}" method="GET" style="display:flex;gap:.5rem;margin-bottom:12px">
    <input name="q" value="{{ old('q', $q) }}" class="form-control" placeholder="Buscar medida o producto (ej: 30cm x 20m)">
    <button class="btn btn-primary">Buscar</button>
  </form>

  @if($q==='') 
    <p>Escribí una medida (p. ej. <b>30cm x 20m</b>) o parte del nombre del producto.</p>
  @elseif($resultados->isEmpty())
    <div class="alert alert-warning">No encontramos coincidencias.</div>
  @else
    <div class="list-group">
      @foreach($resultados as $r)
        <a class="list-group-item list-group-item-action" href="{{ url('/productos/producto/'.$r->producto_id) }}">
          <div class="d-flex justify-content-between">
            <div>
              <div class="fw-bold">{{ $r->producto_nombre }}</div>
              <small class="text-muted">Presentación: {{ $r->medidas ?? $r->nombre_variante }}</small>
            </div>
            <div class="text-end">
              <div>${{ number_format($r->precio,2,',','.') }}</div>
              <small class="text-muted">Stock: {{ $r->stock }}</small>
            </div>
          </div>
        </a>
      @endforeach
    </div>
  @endif
</div>
