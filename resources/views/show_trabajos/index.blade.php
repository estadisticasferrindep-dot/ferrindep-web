@extends('layouts.app')

@section('title','Trabajos')

@section('content')
<div class="container cont-descargas">
    <div class="row justify-content-center">
        <div class="col-md-10">

            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
                <button type="button" class="btn btn-success" style="float:right;"><a style ="color:white;" href="{{route('trabajos.create')}}"><i class="fas fa-plus" style ="color:white; margin-right:7px;" ></i>AÑADIR</a></button>
                <br>

                @foreach ($trabajos as $trabajo)
                
                    <br>
                    <div class="card" style="margin-top:15px;">
                
                        <div class="card-body p-0" >

                            <div style="padding-top:15px;">
                                <div class="container">
                                    <div class="row">
                                        <h4 class="col-4" style="color:#03224e;font-size: 24px; margin-bottom: 15px; margin-left:5px;">{{$trabajo->nombre}}</h4>
                                        <div class="col-2"><img src="{{asset(Storage::url($trabajo->imagen))}}" style="height:60px;max-width: 300px"></td></div>
                                        <div class="col-2"> <td>{{$trabajo->show ? 'Mostrar' : 'No mostrar'}}</td> </div>
                                        <div class="col-3" style="display:flex; "> 
                                            <a style ="color:white; " href="{{route('secciones.create',$trabajo)}}"> <button type="button" class="btn btn-success" style="margin-right:5px; margin-bottom:10px "> <span>AÑADIR SECCIÓN</span></button></a>
                                            <a style ="color:white; " href="{{route('galerias.create',$trabajo)}}"> <button type="button" class="btn btn-success" style="margin-right:5px; margin-bottom:10px "> <span>AÑADIR FOTO</span></button></a>
                                            <a style ="color:white; "href="{{route('trabajos.edit',$trabajo)}}"><button type="button" class="btn btn-primary" style="margin-right:5px; margin-bottom:10px"><i class="far fa-edit"></i></button></a>
                                            <form action="{{route('trabajos.destroy', $trabajo) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger"><a style ="color:white;"href="#"><i class="fas fa-trash-alt"></i></a></button>
                                    
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                                                            
                            <table class="table" style="width: 100%">
                                <thead style="color:#03224e"> 
                                    <tr>
                                        <th scope="col">Título</th>
                                        <th scope="col">Mostrar</th>
                                        <th scope="col">Orden</th>
                                        <th scope="col">Acciones</th>
                                    </tr>
                                </thead>

                                <tbody>  
                                    
                                    @if ($trabajo->secciones)
                                        
                                    @foreach ($trabajo->secciones as $seccion)                            

                                                <tr>
                                                    <td>{{$seccion->titulo}}</td>
                                                    <td>{{$seccion->show ? 'Si' : 'No'}}</td>
                                                    <td>{{$seccion->orden}}</td>
                                                    <td style="display:flex;">
                                                        <button type="button" class="btn btn-primary" style="margin-right:5px;"><a style ="color:white;"href="{{route('secciones.edit',$seccion)}}"><i class="far fa-edit"></i></a></button>
                                                        <form action="{{route('secciones.destroy', $seccion) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" onclick="return confirm('¿Estas seguro?');" class="btn btn-danger"><a style ="color:white;"href="#"><i class="fas fa-trash-alt"></i></a></button>
                                                
                                                        </form>
                                                    </td>
                                                </tr>
                                    @endforeach

                                    @endif
                                    <tr>
                                        <th scope="col">Imagen</th>
                                        <th scope="col">Mostrar</th>
                                        <th scope="col">Orden</th>
                                        <th scope="col">Acciones</th>
                                    </tr>

                                    @if ($trabajo->galerias)
                                        
                                    @foreach ($trabajo->galerias as $galeria)                            

                                                <tr>
                                                    <td > <img src="{{asset(Storage::url($galeria->imagen))}}" style="height:60px;max-width: 300px"></td>
                                                    <td>{{$galeria->show ? 'Si' : 'No'}}</td>
                                                    <td>{{$galeria->orden}}</td>
                                                    <td style="display:flex;">
                                                        <button type="button" class="btn btn-primary" style="margin-right:5px;"><a style ="color:white;"href="{{route('galerias.edit',$galeria)}}"><i class="far fa-edit"></i></a></button>
                                                        <form action="{{route('galerias.destroy', $galeria) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" onclick="return confirm('¿Estas seguro?');" class="btn btn-danger"><a style ="color:white;"href="#"><i class="fas fa-trash-alt"></i></a></button>
                                                
                                                        </form>
                                                    </td>
                                                </tr>
                                    @endforeach

                                    @endif


                                    
                                </tbody>
                            </table>    

                            <hr style="font-weight: bold; color:black">

                        </div>
                    </div>
                    @endforeach
        </div>
    </div>
</div>
@endsection