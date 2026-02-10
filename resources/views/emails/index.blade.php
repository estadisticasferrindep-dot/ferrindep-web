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
                
                <br>
                <br>
                <div class="card" style="margin-top:15px;">

                    <div class="card-body p-0" >

                        <table class="table">
                            <thead style="color:#03224e"> 
                                <tr>
                                    <th scope="col" >Email</th>
                                    <th scope="col">Fecha</th>
                                    <th scope="col">Borrar</th>
                                </tr>
                            </thead>
                            <tbody>  

                                @foreach ($emails as $email)
                                <tr>
                                    <td>{{$email->email}}</td>
                                    <td>{{$email->created_at}}</td>
                                    <td >
                                        <div style="display:flex; align-items:center">
                                        <form action="{{route('emails.destroy', $email) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger"><a style ="color:white;"href="#"><i class="fas fa-trash-alt"></i></a></button>
                                
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