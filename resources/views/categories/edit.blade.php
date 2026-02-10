@extends('layouts.app')

@section('title','Clase')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <a style ="color:grey;" href="{{route('categories.index')}}"><i class="fas fa-arrow-circle-left" style ="color:grey; margin-right:6px;"></i>Volver al listado de clases</a>

            <div class="card" style="margin-top:15px;">

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        <form action="{{route('categories.update',$category)}}" enctype="multipart/form-data" method="POST">
                            @csrf
                            @method('put')

                            @include('categories.form')

                        </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection