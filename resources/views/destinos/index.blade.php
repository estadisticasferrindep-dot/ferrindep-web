@extends('layouts.app')

@section('title','Destinos')

@section('content')
<div class="container">
    <div class="row justify-content-center">
    <div class="col-md-10">

@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif
    <a style ="color:white;" href="{{route('destinos.create')}}"><button type="button" class="btn btn-success" style="float:right;"><i class="fas fa-plus" style ="color:white; margin-right:7px;" ></i>AÑADIR</button></a>
    <br>
    <br>
    <div class="card" style="margin-top:15px;">

        <div class="card-body p-0" >

            <table class="table">
                <thead style="color:#03224e"> 
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Zona</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>  

                    @foreach ($destinos as $destino)
                    <tr>
                        <td>{{$destino->id}}</td>
                        <td>{{$destino->nombre}}</td>
                        <td>{{$destino->zona_id}}</td>
                        <td >
                            <div style="display:flex; align-items:center">
                                <a style ="color:white; "href="{{route('destinos.edit',$destino)}}"><button type="button" class="btn btn-primary" style="margin-right:5px;"><i class="far fa-edit"></i></button></a>
                                <form action="{{route('destinos.destroy', $destino) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('¿Estas seguro?');" class="btn btn-danger"><a style ="color:white;"href="#"><i class="fas fa-trash-alt"></i></a></button>
                        
                                </form>
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