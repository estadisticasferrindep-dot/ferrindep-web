@extends('layouts.app')

@section('title','Metadatos')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">

            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

                <br>
                <br>
                <div class="card" style="margin-top:15px;">

                    <div class="card-body p-0" >

                        <table class="table">
                            <thead style="color:#03224e"> 
                                <tr>
                                    <th scope="col">Sección</th>
                                    <th scope="col">Keywords</th>
                                    <th scope="col">Descripción</th>
                                </tr>
                            </thead>
                            <tbody>  

                                @foreach ($metadatos as $metadato)
                                <tr>
                                    <td style="font-size:20px">{!!$metadato->seccion!!}</td>
                                    <td>{{$metadato->keywords }}</td>
                                    <td>{{$metadato->descripcion}}</td>
                                    <td >
                                        <div style="display:flex; align-items:center">
                                            <a style ="color:white;"href="{{route('metadatos.edit',$metadato)}}"><button type="button" class="btn btn-primary" style="margin-right:5px;"><i class="far fa-edit"></i></button></a>
                                            
                                        </div>
                                    </td>

                                </tr>
                                @endforeach

                            </tbody>
                            <tfoot> 
                                <tr>

                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
        </div>
    </div>
</div>
@endsection