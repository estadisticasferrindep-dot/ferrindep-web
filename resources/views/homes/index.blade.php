@extends('layouts.app')

@section('title','Home')

@section('content')


<div class="container cont-home">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif


                    <?php $home = $homes->first() ?>
                                
                    <form action="{{route('homes.update',$home)}}" enctype="multipart/form-data" method="POST" >
                                
                        @csrf
                        
                        @method('put')
                        
                        <div class="form-group">
                            <label><h4 class="primer-h4">Logo del encabezado</h4></label>
                            <hr>
                            <input type="file" accept="image/*" name="logo" value="{{old('logo', $home->logo)}}" class="form-control-file" >
                        </div>
                        

                        <div class="form-group">
                            <label><h4 class="primer-h4">Logo del pie de página</h4></label>
                            <hr>
                            <input type="file" accept="image/*" name="logo_footer" value="{{old('logo_footer', $home->logo_footer)}}" class="form-control-file" >
                        </div>


                        <div class="form-group">
                            <label><h4 class="primer-h4">Frase del pie de página</h4></label>
                            <input name="frase_footer" value="{{old('frase_footer', $home->frase_footer)}}">
                        </div>



                        <hr>
            

                        <div class="form-group col-md-4">
                            <label>Video (solo el código) </label>
                            <input type="text" name="video" value="{{old('video', $home->video)}}" class="form-control" >
                        </div>

                        <hr>

                        <h4>Sección debajo del slidder</h4>

                        <h5>Primer item</h5>

                        <div class="form-group">
                            <label>Imagen</label>
                            <input type="file" accept="image/*" name="seccion_foto1" value="{{old('seccion_foto1',$home->seccion_foto1)}}" class="form-control-file" >
                        </div>

                        <div class="form-group col-md-4">
                            <label>Título </label>
                            <input type="text" name="seccion_titulo1" value="{{old('seccion_titulo1', $home->seccion_titulo1)}}" class="form-control" >
                        </div>
                        <div class="form-group col-md-4">
                            <label>Texto </label>
                            <input type="text" name="seccion_texto1" value="{{old('seccion_texto1', $home->seccion_texto1)}}" class="form-control" >
                        </div>

                        <h5>Segundo item</h5>
                        <div class="form-group">
                            <label>Imagen</label>
                            <input type="file" accept="image/*" name="seccion_foto2" value="{{old('seccion_foto2',$home->seccion_foto2)}}" class="form-control-file" >
                        </div>

                        <div class="form-group col-md-4">
                            <label>Título </label>
                            <input type="text" name="seccion_titulo2" value="{{old('seccion_titulo2', $home->seccion_titulo2)}}" class="form-control" >
                        </div>
                        <div class="form-group col-md-4">
                            <label>Texto </label>
                            <input type="text" name="seccion_texto2" value="{{old('seccion_texto2', $home->seccion_texto2)}}" class="form-control" >
                        </div>


                        <h5>Tercer item</h5>
                        <div class="form-group">
                            <label>Imagen</label>
                            <input type="file" accept="image/*" name="seccion_foto3" value="{{old('seccion_foto3',$home->seccion_foto3)}}" class="form-control-file" >
                        </div>

                        <div class="form-group col-md-4">
                            <label>Título </label>
                            <input type="text" name="seccion_titulo3" value="{{old('seccion_titulo3', $home->seccion_titulo3)}}" class="form-control" >
                        </div>
                        <div class="form-group col-md-4">
                            <label>Texto </label>
                            <input type="text" name="seccion_texto3" value="{{old('seccion_texto3', $home->seccion_texto3)}}" class="form-control" >
                        </div>



                        <hr>

                        <!-- <h4>Sección Accesorios</h4>
                        <hr>

                        {{-- <div class="form-group col-md-4">
                            <label>Frase </label>
                            <input type="text" name="fogo_frase" value="{{old('fogo_frase', $home->fogo_frase)}}" class="form-control" >
                        </div> --}}
                        <div class="form-group">
                            <label>Imagen</label>
                            <input type="file" accept="image/*" name="fogo_foto" value="{{old('fogo_foto',$home->fogo_foto)}}" class="form-control-file" >
                        </div>


                        <h4>Sección Repuestos</h4>
                        <hr>
                        {{-- <div class="form-group col-md-4">
                            <label>Frase </label>
                            <input type="text" name="coc_frase" value="{{old('coc_frase', $home->coc_frase)}}" class="form-control" >
                        </div> --}}
                        <div class="form-group">
                            <label>Imagen</label>
                            <input type="file" accept="image/*" name="coc_foto" value="{{old('coc_foto',$home->coc_foto)}}" class="form-control-file" >
                        </div>

                        <h4>Sección Iluminación</h4>
                        <hr>
                        {{-- <div class="form-group col-md-4">
                            <label>Frase </label>
                            <input type="text" name="acc_frase" value="{{old('acc_frase', $home->acc_frase)}}" class="form-control" >
                        </div> --}}
                        <div class="form-group">
                            <label>Imagen</label>
                            <input type="file" accept="image/*" name="acc_foto" value="{{old('acc_foto',$home->acc_foto)}}" class="form-control-file" >
                        </div>
 -->

                        <button type="submit" class="btn btn-primary mb-2" >Actualizar Home</button>
                    </form>
                    <!-- @if (session('info'))
                    <script>
                        alert("{{ session('info') }}");
                    </script>
                    @endif -->
                </div>
            </div>
        </div>
    </div>
</div>

@endsection