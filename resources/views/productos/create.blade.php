@extends('layouts.app')

@section('title','Nuevo Producto')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <a style ="color:grey;" href="{{route('show_productos.index')}}"><i class="fas fa-arrow-circle-left" style ="color:grey; margin-right:6px;"></i>Volver al listado de productos</a>

            <div class="card" style="margin-top:15px;">

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        <form action="{{route('productos.store')}}" enctype="multipart/form-data" method="POST">
                            @csrf

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Orden</label>
                                    <input type="text" name="orden" value="{{old('orden')}}" class="form-control" placeholder="Orden">
                                </div>
                                <br>

                                <div class="form-check col-md-4 ">
                                    <input class="form-check-input" type="checkbox" {{ old('con_nombre') == 1 ? 'checked' : '' }} name="con_nombre" value="1">
                                    <label class="form-check-label">Producto con nombre</label>
                                </div>
                                <br>

                                <div class="form-group col-md-4">
                                    <label>Nombre </label>
                                    <input type="text" name="nombre" value="{{old('nombre')}}" class="form-control" placeholder="Nombre">
                                </div> 
                                {{-- <div class="form-group col-md-4">
                                    <label>Precio (sin diámetro)</label>
                                    <input step=".01" type="number" name="precio" value="{{old('precio')}}" class="form-control">
                                </div> --}}
                                
                                <div class="form-group col-md-4">
                                    <label>Video (código) </label>
                                    <input type="text" name="video" value="{{old('video')}}" class="form-control" placeholder="Video">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Cantidad vendida</label>
                                    <input step=".01" type="number" name="vendidos" value="{{old('vendidos')}}" class="form-control">
                                </div>
                            </div>


                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Elige un ancho/categoría para el producto</label>
                                    <select class="form-control" name="categoria_id">
                                        <option disabled>Elige un ancho/categoría...</option>
                                        @foreach ($categorias as $categoria)
                                            <option {{ old('categoria_id') == $categoria->id ? 'selected' : '' }} value="{{$categoria->id}}"> {{$categoria->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Elige unas medidas</label>
                                    <select class="form-control" name="medida_id">
                                        <option disabled>Elige unas medidas...</option>
                                        @foreach ($medidas as $medida)
                                            <option {{ old('medida_id') == $medida->id ? 'selected' : '' }} value="{{$medida->id}}"> {{$medida->medidas}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Elige un espesor para el producto</label>
                                    <select class="form-control" name="espesor_id">
                                        <option disabled>Elige una categoría...</option>
                                        @foreach ($espesores as $espesor)
                                            <option {{ old('espesor_id') == $espesor->id ? 'selected' : '' }} value="{{$espesor->id}}"> {{$espesor->espesor}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Elige una familia para el producto</label>
                                    <select class="form-control" name="familia_id">
                                        <option disabled>Elige una familia...</option>
                                        @foreach ($familias as $familia)
                                            <option {{ old('familia_id') == $familia->id ? 'selected' : '' }} value="{{$familia->id}}"> {{$familia->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                {{-- <div class="form-group col-md-8">
                                    <label>Descripción </label>
                                    <input type="text" name="descripcion" value="{{old('descripcion')}}" class="form-control" placeholder="Descripción">
                                </div> --}}
                            </div>
                            

                            <div class="form-check ">
                                <input class="form-check-input" type="checkbox" {{ old('destacado') == 1 ? 'checked' : '' }} name="destacado" value="1">
                                <label class="form-check-label">Mostrar en Home</label>
                            </div>


                            <div class="form-check ">
                                <input class="form-check-input" type="checkbox" {{ old('show') == 1 ? 'checked' : '' }} name="show" value="1">
                                <label class="form-check-label">Mostrar</label>
                            </div>


                            
                            <div class="form-check ">
                                <input class="form-check-input" type="checkbox" {{ old('oferta') == 1 ? 'checked' : '' }} name="oferta" value="1">
                                <label class="form-check-label">Está en oferta</label>
                            </div>

                            <div class="form-check ">
                                <input class="form-check-input" type="checkbox" {{ old('anular_envio') == 1 ? 'checked' : '' }} name="anular_envio" value="1">
                                <label class="form-check-label">Anula envío</label>
                            </div>

                            <div class="form-group">
                                <label>Imagen</label>
                                <input type="file" accept="image/*" name="imagen" value="{{old('imagen')}}" class="form-control-file" >
                            </div>

                            {{-- <div class="form-group">
                                <label><h4 class="primer-h4">Especificaciones</h4></label>
                                <hr>
                                <textarea class="form-control summernote" name="especificaciones"  rows="4">{{old('especificaciones')}}</textarea>
                            </div> --}}

                            <div class="form-group">
                                <label><h4 class="primer-h4">Descripción</h4></label>
                                <hr>
                                <textarea class="form-control summernote" name="descripcion"  rows="4">{{old('descripcion')}}</textarea>
                            </div>

                            <div class="form-group">
                                <label><h4 class="primer-h4">CaracterÍsticas</h4></label>
                                <hr>
                                <textarea class="form-control summernote" name="caracteristicas"  rows="4">{{old('caracteristicas')}}</textarea>
                            </div>

                            <div class="form-group">
                                <label><h4 class="primer-h4">Usos</h4></label>
                                <hr>
                                <textarea class="form-control summernote" name="usos"  rows="4">{{old('usos')}}</textarea>
                            </div>
                            {{-- <div class="form-group col-md-4">
                                <label>Precio anterior (sin diámetro)</label>
                                <input type="number" step=".01" name="precio_anterior" value="{{old('precio_anterior')}}" class="form-control">
                            </div> --}}

                            {{-- <div class="form-check ">
                                <input class="form-check-input" type="checkbox" {{ old('hay_stock') == 1 ? 'checked' : '' }} name="hay_stock" value="1">
                                <label class="form-check-label">Hay stock</label>
                            </div> --}}


                            <!-- <div class="form-group col-md-4">
                                <label>Elige tres poductos relacionados</label>
                                <select class="form-control" name="relacionado_1">
                                    <option disabled>Elige una producto...</option>
                                    @foreach ($relacionados as $relacionado)
                                        <option {{ old('relacionado_1') == $relacionado->id ? 'selected' : '' }} value="{{$relacionado->id}}"> {{$relacionado->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <select class="form-control" name="relacionado_2">
                                    <option disabled>Elige una producto...</option>
                                    @foreach ($relacionados as $relacionado)
                                        <option {{ old('relacionado_2') == $relacionado->id ? 'selected' : '' }} value="{{$relacionado->id}}"> {{$relacionado->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <select class="form-control" name="relacionado_3">
                                    <option disabled>Elige una producto...</option>
                                    @foreach ($relacionados as $relacionado)
                                        <option {{ old('relacionado_3') == $relacionado->id ? 'selected' : '' }} value="{{$relacionado->id}}"> {{$relacionado->nombre}}</option>
                                    @endforeach
                                </select>
                            </div> -->




                            <button type="submit" class="btn btn-primary mb-2">Enviar producto</button>

                        </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection