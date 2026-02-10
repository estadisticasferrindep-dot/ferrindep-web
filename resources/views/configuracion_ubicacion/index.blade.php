@extends('layouts.app')

@section('title', 'Configuración de Ubicaciones')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">

                @if (session('info'))
                    <div class="alert alert-success" role="alert">
                        {{ session('info') }}
                    </div>
                @endif
                <a style="color:white;" href="{{route('configuracion_ubicacion.create')}}"><button type="button"
                        class="btn btn-success" style="float:right;"><i class="fas fa-plus"
                            style="color:white; margin-right:7px;"></i>AÑADIR</button></a>
                <br>
                <br>
                <div class="card" style="margin-top:15px;">

                    <div class="card-body p-0">

                        <table class="table">
                            <thead style="color:#03224e">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Ciudad Detectada</th>
                                    <th scope="col">Destino Asignado</th>
                                    <th scope="col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($mapeos as $mapeo)
                                    <tr>
                                        <td>{{$mapeo->id}}</td>
                                        <td>{{$mapeo->ciudad_detectada}}</td>
                                        <td>
                                            @if($mapeo->destino)
                                                {{$mapeo->destino->nombre}}
                                            @else
                                                <span class="text-danger">Destino no encontrado</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div style="display:flex; align-items:center">
                                                <a style="color:white; "
                                                    href="{{route('configuracion_ubicacion.edit', $mapeo->id)}}"><button
                                                        type="button" class="btn btn-primary" style="margin-right:5px;"><i
                                                            class="far fa-edit"></i></button></a>
                                                <form action="{{route('configuracion_ubicacion.destroy', $mapeo->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        onclick="return confirm('¿Estas seguro de eliminar este mapeo?');"
                                                        class="btn btn-danger"><a style="color:white;" href="#"><i
                                                                class="fas fa-trash-alt"></i></a></button>

                                                </form>
                                            </div>
                                        </td>

                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection