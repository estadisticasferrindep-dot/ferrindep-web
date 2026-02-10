@extends('layouts.app')

@section('title','Noticias')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">

            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
                <a style ="color:white;" href="{{route('noticias.create')}}"><button type="button" class="btn btn-success" style="float:right;"><i class="fas fa-plus" style ="color:white; margin-right:7px;" ></i>AÑADIR</button></a>
                <br>
                <br>
                <div class="card" style="margin-top:15px;">

                    <div class="card-body p-0" >

                        <table class="table">
                            <thead style="color:#03224e"> 
                                <tr>
                                    <th scope="col">Imagen</th>
                                    <th scope="col">Título</th>
                                    <th scope="col">Mostrar</th>
                                    <th scope="col">Mostrar en Home</th>
                                    <th scope="col">Orden</th>
                                    <th scope="col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>  

                                @foreach ($noticias as $noticia)
                                <tr>
                                    <td> <img src="{{asset(Storage::url($noticia->imagen))}}" style="height:60px;"></td>
                                    <td>{{$noticia->titulo}}</td>
                                    <td>{{$noticia->show ? 'Si' : 'No'}}</td>
                                    <td>{{$noticia->home ? 'Si' : 'No'}}</td>
                                    <td>{{$noticia->orden}}</td>
                                    <td  style="display: flex">
                                        <a style ="color:white; "href="{{route('noticias.edit',$noticia)}}"><button type="button" class="btn btn-primary" style="margin-right:5px;"><i class="far fa-edit"></i></button></a>
                                        <form action="{{route('noticias.destroy', $noticia) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('¿Estas seguro?');" class="btn btn-danger"><a style ="color:white;"href="#"><i class="fas fa-trash-alt"></i></a></button>
                                
                                        </form>
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