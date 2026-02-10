@extends('layouts.plantilla')

@section('title','Descargas')

@section('content')


<section class="section-descargas">
    <div class="container">
        <div class="row">
            <p>Aquí podrás encontrar toda la información técnica necesaria sobre cada equipo. Hay disponibles tablas de corte, manuales, técnicas de corte, etc.</p>
        </div>
        <div class="row">
            <div class="col-4" >
                <div class="list-group  list-group-flush">
                    @foreach ($descargables as $item)
                        @if ($item->show)
                            <a href="{{route('web.descargas', $item->id)}}" style="{{$desc_id == $item->id ? 'color: white; background-color:#04367D;' : ''}}" class="list-group-item list-group-item-action">{{$item->name}}</a>

                        @endif
                        
                    @endforeach
                </div>

            </div>
            <div class="col-8" style="padding-left:42px;">
                <table class="table-descargas" >
                    <thead> 
                        <tr>
                            <th scope="col" style="width:65%; text-align:left;">NOMBRE</th>
                            <th scope="col">FORMATO</th>
                            <th scope="col">DESCARGAR</th>
                        </tr>
                    </thead>
                    <tbody> 
                        @foreach ($infos as $info)
                                                    <tr>
                            <td style="text-align:left;">{{$info->nombre}}</td>
                            <td>{{$info->formato}}</td>
                            <td><a href="{{$info->descarga}}"><i class="fas fa-download"></i></a></td>
                        </tr>
                        @endforeach

                    </tbody> 
                </table>   
            </div>     
        </div>
    <div>
</section>

@endsection