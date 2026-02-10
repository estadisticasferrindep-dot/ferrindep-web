@extends('layouts.app')

@section('title','Clientes')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">

            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
                {{-- <button type="button" class="btn btn-success" style="float:right;"><a style ="color:white;" href="{{route('servicios.create')}}"><i class="fas fa-plus" style ="color:white; margin-right:7px;" ></i>AÑADIR</a></button> --}}
                <br>
                <br>
                <div class="card" style="margin-top:15px;">

                    <div class="card-body p-0" >

                        <table class="table">
                            <thead style="color:#03224e"> 
                                <tr>
                                    <th scope="col">Nombre</th>
                                    <th scope="col">E-mail</th>
                                    <th scope="col">Empresa</th>
                                    <th scope="col">Teléfono</th>
                                    <th scope="col">CUIT</th>
                                    <th scope="col">Dirección</th>
                                    <th scope="col"> Tipo </th>


                                </tr>
                            </thead>
                            <tbody>  

                                @foreach ($usuarios as $usuario)
                                <tr>
                                    <td>{{$usuario->nombre}}</td>
                                    <td>{{$usuario->email }}</td>
                                    <td>{{$usuario->empresa}}</td>
                                    <td>{{$usuario->telefono}}</td>
                                    <td>{{$usuario->cuit}}</td>
                                    <td>{{$usuario->direccion}}</td>
                                    <td>{{$usuario->tipo_cliente}}</td>

                                    
                                    <td  style="display: flex">
                                        <a style ="color:white; "href="{{route('usuarios.edit',$usuario)}}"><button type="button" class="btn btn-primary" style="margin-right:5px;"><i class="far fa-edit"></i></button></a>
                                        <form action="{{route('usuarios.destroy', $usuario) }}" method="POST">
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