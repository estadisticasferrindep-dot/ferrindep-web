@extends('layouts.app')

@section('title','Actualizar producto')

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

                        <form action="{{route('productos.update',$producto)}}" enctype="multipart/form-data" method="POST">
                            @csrf
                            @method('put')
        
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Orden</label>
                                    <input type="text" name="orden" value="{{old('orden', $producto->orden)}}" class="form-control" placeholder="Orden">
                                </div>
                                <br>
                                <div class="form-check col-md-4">
                                    <input class="form-check-input" type="checkbox" {{ old('con_nombre', $producto->con_nombre) == 1 ? 'checked' : '' }} name="con_nombre" value="1">
                                    <label class="form-check-label">Producto con nombre</label>
                                </div>
                                <br>

                                 <div class="form-group col-md-4">
                                    <label>Nombre </label>
                                    <input type="text" name="nombre" value="{{old('nombre', $producto->nombre)}}" class="form-control" placeholder="Nombre">
                                </div> 
                                {{-- <div class="form-group col-md-4">
                                    <label>Precio (sin diámetro)</label>
                                    <input step=".01" type="number" name="precio" value="{{old('precio', $producto->precio)}}" class="form-control">
                                </div> --}}

                                <div class="form-group col-md-4">
                                    <label>Video (código) </label>
                                    <input type="text" name="video" value="{{old('video', $producto->video)}}" class="form-control" placeholder="Video">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Cantidad vendida</label>
                                    <input step=".01" type="number" name="vendidos" value="{{old('vendidos', $producto->vendidos)}}" class="form-control">
                                </div>
                            </div>


                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Elige un ancho/categoría para el producto</label>
                                    <select class="form-control" name="categoria_id">
                                        <option disabled>Elige un ancho/categoría...</option>
                                        @foreach ($categorias as $categoria)
                                            <option {{ old('categoria_id', $producto->categoria_id) == $categoria->id ? 'selected' : '' }} value="{{$categoria->id}}"> {{$categoria->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Elige unas medidas</label>
                                    <select class="form-control" name="medida_id">
                                        <option disabled>Elige unas medidas...</option>
                                        @foreach ($medidas as $medida)
                                            <option {{ old('medida_id', $producto->medida_id) == $medida->id ? 'selected' : '' }} value="{{$medida->id}}"> {{$medida->medidas}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Elige un espesor para el producto</label>
                                    <select class="form-control" name="espesor_id">
                                        <option disabled>Elige una categoría...</option>
                                        @foreach ($espesores as $espesor)
                                            <option {{ old('espesor_id', $producto->espesor_id) == $espesor->id ? 'selected' : '' }} value="{{$espesor->id}}"> {{$espesor->espesor}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>Elige una familia para el producto</label>
                                    <select class="form-control" name="familia_id">
                                        <option disabled>Elige una familia...</option>
                                        @foreach ($familias as $familia)
                                            <option {{ old('familia_id',$producto->familia_id) == $familia->id ? 'selected' : '' }} value="{{$familia->id}}"> {{$familia->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                {{-- <div class="form-group col-md-8">
                                    <label>Descripción </label>
                                    <input type="text" name="descripcion" value="{{old('descripcion', $producto->descripcion)}}" class="form-control" placeholder="Descripción">
                                </div> --}}
                            </div>
                            

                            <div class="form-check ">
                                <input class="form-check-input" type="checkbox" {{ old('destacado', $producto->destacado) == 1 ? 'checked' : '' }} name="destacado" value="1">
                                <label class="form-check-label">Mostrar en Home</label>
                            </div>


                            <div class="form-check ">
                                <input class="form-check-input" type="checkbox" {{ old('show', $producto->show) == 1 ? 'checked' : '' }} name="show" value="1">
                                <label class="form-check-label">Mostrar</label>
                            </div>


                            
                            <div class="form-check ">
                                <input class="form-check-input" type="checkbox" {{ old('oferta', $producto->oferta) == 1 ? 'checked' : '' }} name="oferta" value="1">
                                <label class="form-check-label">Está en oferta</label>
                            </div>

                            <div class="form-check ">
                                <input class="form-check-input" type="checkbox" {{ old('anular_envio',$producto->anular_envio) == 1 ? 'checked' : '' }} name="anular_envio" value="1">
                                <label class="form-check-label">Anula envío</label>
                            </div>

                            <div class="form-group">
                                <label>Imagen</label>
                                <input type="file" accept="image/*" name="imagen" value="{{old('imagen', $producto->imagen)}}" class="form-control-file" >
                            </div>

                            {{-- <div class="form-group">
                                <label><h4 class="primer-h4">Especificaciones</h4></label>
                                <hr>
                                <textarea class="form-control summernote" name="especificaciones"  rows="4">{{old('especificaciones', $producto->especificaciones)}}</textarea>
                            </div> --}}

                            <div class="form-group">
                                <label><h4 class="primer-h4">Descripción</h4></label>
                                <hr>
                                <textarea class="form-control summernote" name="descripcion"  rows="4">{{old('descripcion', $producto->descripcion)}}</textarea>
                            </div>

                            <div class="form-group">
                                <label><h4 class="primer-h4">CaracterÍsticas</h4></label>
                                <hr>
                                <textarea class="form-control summernote" name="caracteristicas"  rows="4">{{old('caracteristicas', $producto->caracteristicas)}}</textarea>
                            </div>

                            <div class="form-group">
                                <label><h4 class="primer-h4">Usos</h4></label>
                                <hr>
                                <textarea class="form-control summernote" name="usos"  rows="4">{{old('usos', $producto->usos)}}</textarea>
                            </div>
                            {{-- <div class="form-group col-md-4">
                                <label>Precio anterior (sin diámetro)</label>
                                <input type="number" step=".01" name="precio_anterior" value="{{old('precio_anterior', $producto->precio_anterior)}}" class="form-control">
                            </div> --}}

                            {{-- <div class="form-check ">
                                <input class="form-check-input" type="checkbox" {{ old('hay_stock', $producto->hay_stock) == 1 ? 'checked' : '' }} name="hay_stock" value="1">
                                <label class="form-check-label">Hay stock</label>
                            </div> --}}


                            <!-- <div class="form-group col-md-4">
                                <label>Elige tres poductos relacionados</label>
                                <select class="form-control" name="relacionado_1">
                                    <option disabled>Elige una producto...</option>
                                    @foreach ($relacionados as $relacionado)
                                        <option {{ old('relacionado_1', $producto->relacionado_1) == $relacionado->id ? 'selected' : '' }} value="{{$relacionado->id}}"> {{$relacionado->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <select class="form-control" name="relacionado_2">
                                    <option disabled>Elige una producto...</option>
                                    @foreach ($relacionados as $relacionado)
                                        <option {{ old('relacionado_2', $producto->relacionado_2) == $relacionado->id ? 'selected' : '' }} value="{{$relacionado->id}}"> {{$relacionado->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <select class="form-control" name="relacionado_3">
                                    <option disabled>Elige una producto...</option>
                                    @foreach ($relacionados as $relacionado)
                                        <option {{ old('relacionado_3', $producto->relacionado_3) == $relacionado->id ? 'selected' : '' }} value="{{$relacionado->id}}"> {{$relacionado->nombre}}</option>
                                    @endforeach
                                </select>
                            </div> -->



                            <button type="submit" class="btn btn-primary mb-2">Enviar producto</button>
                        </form>
<br>
<br>
<br>
                            {{-- <button type="button" class="btn btn-success" style="margin-right:5px; margin-bottom:10px "> <a style ="color:white; " href="{{route('coloresP.create',$producto)}}"> <span>AÑADIR COLOR</span></a></button>
                            <table class="table" style="width: 100%">
                                <thead style="color:#03224e"> 
                                    <tr>
                                        <th scope="col">Colores</th>
                                        <th scope="col">Nombre</th>
                                        <th scope="col">Mostrar</th>
                                        <th scope="col">Orden</th>
                                        <th scope="col">Acciones</th>
                                    </tr>
                                </thead>

                                <tbody>  
                            

                                    @if ($producto->colores)
                                        
                                    @foreach ($producto->colores as $color)                            

                                                <tr>
                                                    <td > <img src="{{asset(Storage::url($color->color->imagen))}}" style="height:60px;max-width: 300px"></td>
                                                    <td>{{$color->color->nombre}}</td>
                                                
                                                    <td>{{$color->show ? 'Si' : 'No'}}</td>
                                                    <td>{{$color->orden}}</td>
                                                    <td> 
                                                        <div style="display:flex; align-items:center"> 
                                                            <button type="button" class="btn btn-primary" style="margin-right:5px;"><a style ="color:white;"href="{{route('coloresP.edit',[$color, $producto])}}"><i class="far fa-edit"></i></a></button>
                                                            <form action="{{route('coloresP.destroy', [$color, $producto]) }}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" onclick="return confirm('¿Estas seguro?');" class="btn btn-danger"><a style ="color:white;"href="#"><i class="fas fa-trash-alt"></i></a></button>
                                                    
                                                            </form>
                                                        </div>
                                                        
                                                    </td>
                                                </tr>
                                    @endforeach

                                    @endif


                                    
                                </tbody>
                            </table>    --}}


                            <br>
                            <br>
                            <br>
                            <br>
                            {{-- <button type="button" class="btn btn-success" style="margin-right:5px; margin-bottom:10px "> <a style ="color:white; " href="{{route('diametrosP.create',$producto)}}"> <span>AÑADIR DIAMETRO</span></a></button>
                            <table class="table" style="width: 100%">
                                <thead style="color:#03224e"> 
                                    <tr>
                                        <th scope="col">Diámetros</th>
                                        <th scope="col">Precio</th>
                                        <th scope="col">Precio anterior</th>
                                        <th scope="col">Mostrar</th>
                                        <th scope="col">Orden</th>
                                        <th scope="col">Acciones</th>
                                    </tr>
                                </thead>

                                <tbody>  


                                    @if ($producto->diametros)
                                    
                                    @foreach ($producto->diametros as $diametros)                            

                                                <tr>
                                                    <td>{{$diametros->tamano}}</td>
                                                    <td>{{$diametros->precio}}</td>
                                                    <td>{{$diametros->precio_anterior}}</td>

                                                
                                                    <td>{{$diametros->show ? 'Si' : 'No'}}</td>
                                                    <td>{{$diametros->orden}}</td>
                                                    <td>
                                                        <div style="display:flex; align-items:center">
                                                            <button type="button" class="btn btn-primary" style="margin-right:5px;"><a style ="color:white;"href="{{route('diametrosP.edit',[$diametros,$producto])}}"><i class="far fa-edit"></i></a></button>
                                                            <form action="{{route('diametrosP.destroy', [$diametros,$producto]) }}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" onclick="return confirm('¿Estas seguro?');" class="btn btn-danger"><a style ="color:white;"href="#"><i class="fas fa-trash-alt"></i></a></button>
                                                    
                                                            </form>
                                                        </div>
                                                        
                                                    </td>
                                                </tr>
                                    @endforeach

                                    @endif


                                    
                                </tbody>
                            </table>    --}}


                            {{-- <button type="button" class="btn btn-success" style="margin-right:5px; margin-bottom:10px "> <a style ="color:white; " href="{{route('rangos.create',$producto)}}"> <span>AÑADIR RANGO</span></a></button>
                            <table class="table" style="width: 100%">
                                <thead style="color:#03224e"> 
                                    <tr>
                                        <th scope="col">Rangos</th>
                                        <th scope="col">Precio</th>
                                        <th scope="col">Precio anterior</th>
                                        <th scope="col">Es el último?</th>
                                        <th scope="col">Acciones</th>
                                    </tr>
                                </thead>

                                <tbody>  


                                    @if ($producto->rangos)
                                    
                                    @foreach ($producto->rangos as $rangos)                            

                                                <tr>
                                                    <td> Hasta: {{$rangos->max}} unidades</td>
                                                    <td>{{$rangos->precio}}</td>
                                                    <td>{{$rangos->precio_anterior}}</td>
                                                    <td>{{$rangos->ultimo ? 'Si' : 'No'}}</td>

                                                    <td>
                                                        <div style="display:flex; align-items:center">
                                                            <button type="button" class="btn btn-primary" style="margin-right:5px;"><a style ="color:white;"href="{{route('rangosP.edit',[$rangos,$producto])}}"><i class="far fa-edit"></i></a></button>
                                                            <form action="{{route('rangosP.destroy', [$rangos,$producto]) }}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" onclick="return confirm('¿Estas seguro?');" class="btn btn-danger"><a style ="color:white;"href="#"><i class="fas fa-trash-alt"></i></a></button>
                                                    
                                                            </form>
                                                        </div>
                                                        
                                                    </td>
                                                </tr>
                                    @endforeach

                                    @endif


                                    
                                </tbody>
                            </table>    --}}


                            <button type="button" class="btn btn-success" style="margin-right:5px; margin-bottom:10px "> <a style ="color:white; " href="{{route('presentacionesP.create',$producto)}}"> <span>AÑADIR PRESENTACIÓN</span></a></button>
                            <table class="table" style="width: 100%">
                                <thead style="color:#03224e"> 
                                    <tr>
                                        <th scope="col">Presentación</th>
                                        <th scope="col">Precio</th>
                                        <th scope="col">Precio anterior</th>
                                        @if($producto->con_nombre)
                                        <th scope="col">Medidas</th>
                                        @else
                                        <th scope="col">Metros</th>
                                        @endif

                                        <th scope="col">Límite de compra</th>
                                        <th scope="col">Stock</th>
                                        <th scope="col">Peso</th>
                                        <th scope="col">Envío grats</th>


                                        
                                        <th scope="col">Acciones</th>
                                    </tr>
                                </thead>

                                <tbody>  

 
                                    @if ($producto->presentaciones)
                                    
                                    @foreach ($producto->presentaciones as $presentaciones)                            
                                        
                                                <tr>
                                                    <form id="formulario{{$presentaciones->id}}" action="{{route('presentacionesP2.update', [$presentaciones,$producto])}}"  method="POST">
                                                            @csrf
                                                            @method('put')

                                                    <td>
                                                        {{$presentaciones->nombre}} 
                                                        @if($presentaciones->free || $presentaciones->envio_gratis_zona_1 || $presentaciones->envio_gratis_zona_2 || $presentaciones->envio_gratis_zona_3 || $presentaciones->envio_gratis_zona_4)
                                                            <br><span class="badge badge-success" style="font-size:10px;">Ofrece envío gratis</span>
                                                        @endif
                                                    </td>
                                                    <td> <input  type="number" min="1" name="precio" value="{{old('precio',$presentaciones->precio)}}" class="form-control"></td>

                                                    <td>{{$presentaciones->precio_anterior}}</td>

                                                    @if($producto->con_nombre)
                                                    <td>{{$presentaciones->medidas}}</td>
                                                    @else
                                                    <td>{{$presentaciones->metros}}</td>
                                                    @endif
                                                    

                                                    <td> <input  type="number" min="0" name="limite" value="{{old('limite',$presentaciones->limite)}}" class="form-control"></td>
                                                    <td> <input  type="number" min="0" name="stock" value="{{old('stock',$presentaciones->stock)}}" class="form-control"></td>

                                                    <td>{{$presentaciones->peso}}</td>
                                                    <td>{{$presentaciones->free ? 'Si' : 'No'}}</td>
                                                     

                                                    <td>

                                                    <script type="text/javascript">
                                                        var httpRequest = new XMLHttpRequest();

document.getElementById('formulario{{$presentaciones->id}}').addEventListener('submit', function(event) {
    event.preventDefault(); // Evita que el formulario se envíe normalmente
    
    var formData = new FormData(this); // Obtiene los datos del formulario
    
    httpRequest.onreadystatechange = function() {
        if (httpRequest.readyState === XMLHttpRequest.DONE) {
            if (httpRequest.status === 200) {
                // Maneja la respuesta del servidor
                console.log(httpRequest.responseText);
                // Puedes realizar acciones adicionales aquí, como actualizar la interfaz de usuario.
            } else {
                // Maneja errores si la solicitud falla
                console.error('Hubo un problema con la solicitud.');
            }
        }
    };

    // Abre una solicitud POST a la URL deseada
    httpRequest.open('POST', '{{route('presentacionesP2.update', [$presentaciones,$producto])}}', true);
    
    // Envía la solicitud con los datos del formulario
    httpRequest.send(formData);
});
                                                                </script> 


                                                    <button type="submit" class="btn btn-primary" style="margin-bottom:2px;">ACTUALIZAR</button>
                                                    </form> 
                                                                
                                                        <div style="display:flex; align-items:center">
                                                            <button type="button" class="btn btn-primary" style="margin-right:5px;"><a style ="color:white;"href="{{route('presentacionesP.edit',[$presentaciones,$producto])}}"><i class="far fa-edit"></i></a></button>
                                                            <form action="{{route('presentacionesP.destroy', [$presentaciones,$producto]) }}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" onclick="return confirm('¿Estas seguro?');" class="btn btn-danger"><a style ="color:white;"href="#"><i class="fas fa-trash-alt"></i></a></button>
                                                    
                                                            </form>
                                                        </div>
                                                        
                                                    </td>
                                                </tr>
                                    @endforeach

                                    @endif


                                    
                                </tbody>
                            </table>  

                        
                </div>
            </div>
        </div>
    </div>
</div>

@endsection