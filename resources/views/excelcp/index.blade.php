@extends('layouts.app')

@section('title','Excel Envíos')

@section('content')

<div class="container cont-empresa">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif


                    <form action="{{route('excelcp.import')}}" method="POST" enctype="multipart/form-data">
                    @csrf    
                        <div class="form-group">
                            <input type="file" name="archivo" class="form-control-file" >
                        </div>
                        
                        <div class="form-group">
                        <a class="btn btn-info" href="{{route('excelcp.export')}}" style="margin-top: 20px; color:white;">Descargar datos</a>
                            <button class="btn btn-success" type="submit">Actualizar DB</button>
                        </div>
                    </form>
                    @if (session('info'))
                    <script>
                        alert("{{ session('info') }}");
                    </script>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


@endsection