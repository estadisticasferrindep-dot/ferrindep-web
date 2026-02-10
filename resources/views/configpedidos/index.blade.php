@extends('layouts.app')

@section('title','Configuración de los Pedidos')

@section('content')


<div class="container cont-configpedido">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif


                    <?php $configpedido = $configpedidos->first() ?>
            
                        
                    <form action="{{route('configpedidos.update',$configpedido)}}" enctype="multipart/form-data" method="POST">
                                
                        @csrf
                        
                        @method('put')

                        <div class="form-group col-md-4">
                            <label>Email-1 </label>
                            <input type="text" name="email1" value="{{old('email1', $configpedido->email1)}}" class="form-control" >
                        </div>
                        <div class="form-group col-md-4">
                            <label>Email-2 </label>
                            <input type="text" name="email2" value="{{old('email2', $configpedido->email2)}}" class="form-control" >
                        </div>
                        <div class="form-group col-md-4">
                            <label>Email-3 </label>
                            <input type="text" name="email3" value="{{old('email3', $configpedido->email3)}}" class="form-control" >
                        </div>

                        
                        <h3>Envios</h3>
                        <div class="form-group col-md-6">
                            <label>Retiro en fábrica</label>
                            <input type="number" min="0" name="costo_envio_fabrica" value="{{old('costo_envio_fabrica', $configpedido->costo_envio_fabrica)}}" class="form-control">
                        </div>
                        <div class="form-group">
                            <textarea class="form-control summernote" name="parrafo_envio_fabrica" rows="3">{{old('parrafo_envio_fabrica',$configpedido->parrafo_envio_fabrica)}}</textarea>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Envios CABA y GBA (cálculo con código postal)</label>
                            {{-- <input type="number" min="0" name="costo_envio_caba" value="{{old('costo_envio_caba', $configpedido->costo_envio_caba)}}" class="form-control"> --}}
                        </div>
                        <div class="form-group">
                            <textarea class="form-control summernote" name="parrafo_envio_caba" rows="3">{{old('parrafo_envio_caba',$configpedido->parrafo_envio_caba)}}</textarea>
                        </div>
                        <!-- <div class="form-group col-md-6">
                            <label>Envios al interior</label>
                            <input type="number" min="0" name="costo_envio_interior" value="{{old('costo_envio_interior', $configpedido->costo_envio_interior)}}" class="form-control">
                        </div>
                        <div class="form-group">
                            <textarea class="form-control summernote" name="parrafo_envio_interior" rows="3">{{old('parrafo_envio_interior',$configpedido->parrafo_envio_interior)}}</textarea>
                        </div> -->

                        
                        <div class="form-group col-md-6">
                            <label>Envío gratuito (Monto requerido)</label>
                            <input type="number" min="0" name="costo_envio_expreso" value="{{old('costo_envio_expreso', $configpedido->costo_envio_expreso)}}" class="form-control">
                        </div>
                        <div class="form-group">
                            <textarea class="form-control summernote" name="parrafo_envio_expreso" rows="3">{{old('parrafo_envio_expreso',$configpedido->parrafo_envio_expreso)}}</textarea>
                        </div>
                        <hr>
                        <h3>Descuentos por sistema de pago (en porcentaje)</h3>

                        <div class="form-group col-md-6">
                            <label>Efectivo</label>
                            <input type="number" min="0" name="descuento_efectivo" value="{{old('descuento_efectivo', $configpedido->descuento_efectivo)}}" class="form-control">
                        </div>

                        <div class="form-group">
                            <textarea class="form-control summernote" name="parrafo_efectivo" rows="3">{{old('parrafo_efectivo',$configpedido->parrafo_efectivo)}}</textarea>
                        </div>





                        <div class="form-group col-md-6">
                            <label>Transferencia bancaria</label>
                            <input type="number" min="0" name="descuento_transferencia" value="{{old('descuento_transferencia', $configpedido->descuento_transferencia)}}" class="form-control">
                        </div>

                        <div class="form-group">
                            <textarea class="form-control summernote" name="parrafo_transferencia" rows="3">{{old('parrafo_transferencia',$configpedido->parrafo_transferencia)}}</textarea>
                        </div>





                        <div class="form-group col-md-6">
                            <label>Mercado pago</label>
                            <input type="number" min="0" name="descuento_mp" value="{{old('descuento_mp', $configpedido->descuento_mp)}}" class="form-control">
                        </div>

                        <div class="form-group">
                            <textarea class="form-control summernote" name="parrafo_mp" rows="3">{{old('parrafo_mp',$configpedido->parrafo_mp)}}</textarea>
                        </div>

                        <hr></hr>

                        <br><br><br>

                        <h3>Textos para los mails:</h3>

                        <h4>Registro</h4>

                        <div class="form-group">
                            <textarea class="form-control summernote" name="mail_registro" rows="3">{{old('mail_registro',$configpedido->mail_registro)}}</textarea>
                        </div>

                        <h4>Pago por mercado pago</h4>

                        <div class="form-group">
                            <textarea class="form-control summernote" name="mail_mp" rows="3">{{old('mail_mp',$configpedido->mail_mp)}}</textarea>
                        </div>

                        <h4>Pago por transferencia</h4>

                        <div class="form-group">
                            <textarea class="form-control summernote" name="mail_transferencia" rows="3">{{old('mail_transferencia',$configpedido->mail_transferencia)}}</textarea>
                        </div>
                        <h4>Pago en efectivo</h4>

                        <div class="form-group">
                            <textarea class="form-control summernote" name="mail_efectivo" rows="3">{{old('mail_efectivo',$configpedido->mail_efectivo)}}</textarea>
                        </div>

                        <h4>Retiro en fábrica</h4>

                        <div class="form-group">
                            <textarea class="form-control summernote" name="mail_fabrica" rows="3">{{old('mail_fabrica',$configpedido->mail_fabrica)}}</textarea>
                        </div>
                        <h4>Envío </h4>

                        <div class="form-group">
                            <textarea class="form-control summernote" name="mail_envio" rows="3">{{old('mail_envio',$configpedido->mail_envio)}}</textarea>
                        </div>



                        <button type="submit" class="btn btn-primary mb-2" >Actualizar Configuración</button>
                    </form>
                   
                </div>
            </div>
        </div>
    </div>
</div>


@endsection