@extends('layouts.app')

@section('titulo', 'Configuración de Envíos Flex')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form action="{{ route('shipping-prices.store') }}" method="POST">
                    @csrf

                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Tarifas Base</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($tarifas as $tarifa)
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label><strong>{{ $tarifa->nombre }}</strong></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">$</span>
                                                </div>
                                                <input type="number" step="0.01" class="form-control"
                                                    name="tarifas[{{ $tarifa->id }}]" value="{{ $tarifa->monto }}">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Zonas de Cobertura (Partidos/Localidades)</h5>
                        </div>
                        <div class="card-body">
                            <!-- Add New Zone -->
                            <div class="row mb-4 p-3 bg-light border rounded">
                                <div class="col-md-5">
                                    <label>Nueva Localidad / Partido</label>
                                    <input type="text" class="form-control" name="new_zone_name" placeholder="Ej: Lanus">
                                    <small class="text-muted">Se normalizará a minúsculas automáticamente.</small>
                                </div>
                                <div class="col-md-4">
                                    <label>Asignar a Tarifa</label>
                                    <select class="form-control" name="new_zone_tarifa">
                                        @foreach($tarifas as $tarifa)
                                            <option value="{{ $tarifa->id }}">
                                                {{ $tarifa->nombre }} (Actual: ${{ $tarifa->monto }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-success btn-block">
                                        <i class="fas fa-plus"></i> Agregar Zona
                                    </button>
                                </div>
                            </div>

                            <hr>

                            <!-- List Zones -->
                            <div class="row">
                                @foreach($tarifas as $tarifa)
                                    <div class="col-md-3">
                                        <div class="card shadow-sm h-100">
                                            <div class="card-header text-center font-weight-bold">
                                                {{ $tarifa->nombre }}
                                            </div>
                                            <ul class="list-group list-group-flush"
                                                style="max-height: 300px; overflow-y: auto;">
                                                @if(isset($zonas[$tarifa->id]))
                                                    @foreach($zonas[$tarifa->id] as $zona)
                                                        <li
                                                            class="list-group-item d-flex justify-content-between align-items-center p-2">
                                                            <span class="text-capitalize">{{ $zona->nombre_busqueda }}</span>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input"
                                                                    id="del_zona_{{ $zona->id }}" name="delete_zones[]"
                                                                    value="{{ $zona->id }}">
                                                                <label class="custom-control-label text-danger"
                                                                    for="del_zona_{{ $zona->id }}" title="Marcar para eliminar">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </label>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                @else
                                                    <li class="list-group-item text-muted text-center pt-4">Sin zonas asignadas</li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection