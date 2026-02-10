@extends('layouts.app')

@section('title','Destinos por zonas')

@section('content')
<div class="container">
    <div class="row justify-content-center">
    <div class="col-md-10">

@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif
    <a style ="color:white;" href="{{route('destinozonas.create')}}"><button type="button" class="btn btn-success" style="float:right;"><i class="fas fa-plus" style ="color:white; margin-right:7px;" ></i>AÑADIR</button></a>
    <br>
    <br>
    <div class="card" style="margin-top:15px;">

        <div class="card-body p-0" >

            <table class="table">
                <thead style="color:#03224e"> 
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Destino</th>
                        <th scope="col">Zona</th>
                    </tr>
                </thead>
                <tbody>  

                    @foreach ($destinozonas as $destinozona)
                    <tr>
                        <td>{{$destinozona->destino->nombre}}</td>
                        <td>{{$destinozona->zona == null ? '' : $destinozona->zona->nombre}}</td>

                        <td >
                            <div style="display:flex; align-items:center">
                                <a style ="color:white; "href="{{route('destinozonas.edit',$destinozona)}}"><button type="button" class="btn btn-primary" style="margin-right:5px;"><i class="far fa-edit"></i></button></a>
                                <form action="{{route('destinozonas.destroy', $destinozona) }}" method="POST">
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