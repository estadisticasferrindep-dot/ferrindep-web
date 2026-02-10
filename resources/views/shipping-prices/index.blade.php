@extends('layouts.app')

@section('title','Pedidos')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <form action="" method="POST" enctype="multipart/form-data">
                @csrf
                <table class="table text-center table-bordered table-hover">
                    <thead>
                        <tr>
                            <th></th>
                            @foreach($zonas as $zona)
                                <th>{{ $zona->nombre }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pesos as $peso)
                            <tr>
                                <td>{{ $peso }}</td>
                                @foreach($zonas as $zona)
                                    <td class="p-1"><input type="text" class="form-control" name="prices[{{ $zona->id }}-{{ $peso }}]" value="{{ @$prices[ $zona->id . '-' . $peso ] }}"> </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <button type="submit" class="btn btn-primary">Guardar</button>
                <a href="{{ route('shipping-prices') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection