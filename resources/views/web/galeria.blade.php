@extends('layouts.plantilla')

@section('title','Galería')

@section('content')


{{-- <div class="overlay-galeria">
    <div class="slideshow">
        <span class="btn-cerrar"><i class="fas fa-times"></i></span>
        <div class="botones atras"><i class="fas fa-chevron-left"></i></div>
        <div class="botones adelante"><i class="fas fa-chevron-right"></i></div>

        <img src="{{asset(Storage::url($fotos->first()->imagen))}}" alt="" id="img_slideshow">
    </div>
</div> --}}

<section class="section-galeria"  style="margin-bottom:80px; margin-top:80px;"> 

    <div class="container" style="position:relative;">

        

        {{-- <button class="nav-btn" data-bs-toggle="modal" data-bs-target="#exampleModal2" style="width: 50px; height:50px;"><i class="fas fa-bars"></i></button>
                    
        <div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModal2Label" aria-hidden="true" style="position:absolute; top:0; right:0;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style=" width:500px;">
                        <div>
                            <div class="grid-modal"  style="background-image: url({{asset(Storage::url($fotos->first()->imagen))}}); width:500px; height:700px;" ></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div> --}}
        

        <?php $cantidadFotos = count($fotos) ?>
        <?php $turno= 0 ?>
        @foreach ($fotos->chunk(min(7,$cantidadFotos)) as $key=>$chunk)   
            <div class="grid" style="margin-top:30px;">

                @foreach ($chunk as $key=>$foto)

                    {{-- <div class="grid-{{ $key % 7}}" onClick="toModal({{$key,$chunk[$key]->imagen, $chunk[$key]->descripcion}})" style="background-image: url({{asset(Storage::url($chunk[$key]->imagen))}}); " ></div>                 --}}
                
                
                    
                
                    <button  type="submit" class="grid-{{ $key % 7}} " data-bs-toggle="modal"  data-bs-target="#tabla-producto{{$foto->id}}" onclick="openProductModal('{{asset(Storage::url($foto->imagen))}}#view=Fit&toolbar=0&navpanes=0&scrollbar=0', {{$foto->id}})"  style="background-color: transparent;border: none;">
                        <div class="grid-item"  style="background-image: url({{asset(Storage::url($chunk[$key]->imagen))}}); width:100%; height:100%" ></div>                
                    </button>
            
                    <div class="modal fade " id="tabla-producto{{$foto->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-fullscreen" style="display: flex;align-items: center;justify-content: center;">
                        <div class="modal-content" style=" width: 75%; height:75%; position:relative;">
                            {{-- <div class="botones atras" onclick="openProductModal('{{asset(Storage::url( $fotos[max($turno-1,0)]->imagen ) )}}#view=Fit&toolbar=0&navpanes=0&scrollbar=0', {{ $foto->id  }})"  style="background-color: transparent;border: none;"><i class="fas fa-chevron-left" ></i></div> --}}
                            
                            {{-- <div class="botones adelante"><i class="fas fa-chevron-right"onclick="openProductModal('{{asset(Storage::url( $fotos[min($turno+1,$cantidadFotos-1)]->imagen ) )}}#view=Fit&toolbar=0&navpanes=0&scrollbar=0', {{$fotos[min($turno,$cantidadFotos-1)]->id}})"  style="background-color: transparent;border: none;"></i></div> --}}
                            <div class="modal-header">
            
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body" style="display:flex; justify-content:center; align-items:center;">
                            <img src="" id="iframe-producto{{$foto->id}}" alt="" class="img-fluid" style="max-width: 100%; max-height:100%;">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                        </div>
                    </div>
                
                    <?php $turno= $turno+1 ?>
                
                
                
                
                
                
                
                
                
                
                
                @endforeach
            </div>
        @endforeach
    </div>
</section> 
@endsection