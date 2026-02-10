<template>

<div>
    
    

        <!-- <div @mouseover="mouseOver" v-show="!hover"  > -->
        <div  v-show="!hover"  >

        <!-- <a :href="urlProducto" style="text-decoration: none">
            <div class="img-border-categorias img-active" :style="'background-image: background-image: url('+ imagen + ');'">
                
                
                <div v-show="oferta" class="oferta">OFERTA</div> 
            </div>
            </a>     -->
        <hr>

        <div class="text-box-categorias">
            <div v-if="conNombre" class="tabla-trabajo" style="font: normal normal bold 12px/17px Open Sans;
            color: #939292;
            margin-top:16px;">
                <span   style="font-size:120%">{{ancho}}  </span> 


            </div>
            <div v-else-if="(ancho >= 100)" class="tabla-trabajo" style="font: normal normal bold 12px/17px Open Sans;
            color: #939292;
            margin-top:16px;">
                <span   style="font-size:120%">{{ancho/100}} m </span> alto/ancho 


            </div>
            <div  v-else class="tabla-trabajo" style="font: normal normal bold 12px/17px Open Sans;
            color: #939292;
            margin-top:16px;">
                <span  style="font-size:120%"> {{ancho}} cm  </span> alto/ancho 


            </div>
            <div  v-if="conNombre" class="tabla-trabajo" style="font: normal normal bold 21px/28px Open Sans;">
                {{nombre}} 
            </div>
            <div  v-else class="tabla-trabajo" style="font: normal normal bold 21px/28px Open Sans;">
                {{medidas}} {{espesor}} 
            </div>
            <hr>
        </div>
        </div>

        <!-- <div @mouseleave="mouseOver" v-show="hover"  > -->
        <div  v-show="hover"  >

                
                <!-- <a :href="urlProducto" >
                    <button class="detalle"><i class="far fa-eye"></i>  VER DETALLE</button>
                </a> -->

                <a :href="urlProducto" >
                    <button class="agregar" ><i class="fas fa-shopping-cart"></i> AGREGAR AL CARRITO</button>
                </a>
                <div class="text-box-categorias"> 
                    <div v-if="conNombre" class="tabla-trabajo" style="font: normal normal bold 21px/28px Open Sans;">
                        {{nombre}} 
                    </div>
                    <div  v-else class="tabla-trabajo" style="font: normal normal bold 21px/28px Open Sans;">
                        {{medidas}} {{espesor}} 
                    </div>
                    <hr>
                </div>
        </div>
    
</div>

</template>

<script>
    export default {
        name: 'AddToCartButton',
        props: { 
            ancho: {type: String},
            medidas: {type: String},
            espesor: {type: String},
            nombre: {type: String},
            conNombre: {type: Number},

            urlProducto:{type: String},
            oferta: {type: Boolean}

        },
        data() { // lo que tengo de mi componente, sus datos, tambien las globales con window
            return {
                hover:0

            };
        },
        created() { // lo que aparece cuando se crea el componente
            
        },
        methods: {
            mouseOver(){
            this.hover = !this.hover;   
            }
        },
        mouseLeave(){
            this.hover = !this.hover;   
        }
    };
</script>

<style scoped>

    .detalle{
        width: 100%;
        border: 1px solid #FD914D;
        border-radius: 8px;
        padding:6px 10px;
        text-align: center;
        font: normal normal bold 14px/19px Open Sans;
        letter-spacing: 0.56px;
        color: #FD914D;
        background-color: white;
    }

    .agregar{
        width: 100%;
        border: 1px solid #FD914D;
        border-radius: 8px;
        padding:6px 10px;
        text-align: center;
        font: normal normal bold 14px/19px Open Sans;
        letter-spacing: 0.56px;
        color: #FFFFFF;
        background: #FD914D 0% 0% no-repeat padding-box;
        margin-bottom: 17px;

    }


</style>