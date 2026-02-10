@extends('layouts.app')

@section('title','Categorías')

@section('content')
<style>
    .list-group {
        border-radius: 0;
    }
    .list-group-item {
        position: relative;
    }
    .list-group-item > .btn-group {
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        border-radius: 0;
    }
    .list-group-item > .btn-group > .btn {
        border-radius: 0;
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">

            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
                <button type="button" class="btn btn-success" style="float:right;"><a style ="color:white;" href="{{route('categories.create')}}"><i class="fas fa-plus" style ="color:white; margin-right:7px;" ></i>AÑADIR</a></button>
                <br>
                <br>
                @include('categories.categories-list', ['categories' => $categories, 'type' => 'list-group'])
        </div>
    </div>
</div>
@endsection