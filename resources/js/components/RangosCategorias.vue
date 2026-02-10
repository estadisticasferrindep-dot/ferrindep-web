<template>

<div>

    <div v-if="rangos2.length >= 1">
        
        <div class="container-fluid" style="padding:0">
            <div class="row" style="    display: flex;justify-content: center;" >
                
                <!-- <div  v-show="rango.show" :key="key"> -->

                    <div class=" rango"  v-for="(rango, key) in JSON.parse(rangos)" :key="key" :class=" rango.id == rangoElegido.id  ? 'select' : '' "  @click="selectRango(rango)"  > 
                        
                        <span v-if="key==0">1-{{rango.max}} unidades</span>
                        <span v-else-if="parseFloat(rango.ultimo)"> Más de {{rangos2[key-1].max}} uni.</span>
                        
                        <span v-else >{{parseFloat(rangos2[Math.max(key-1,0)].max) +1}}-{{rango.max}} unidades  </span>
                        
                    </div>
                <!-- </div> -->

            </div> 
            <hr>
            <div class="col-12" v-if="rangoElegido">
                <p class="precio" style="text-align: center;" v-if="rangoElegido.tprecio"><span v-if="parseFloat(oferta)" class="precio-oferta">${{rangoElegido.precio_anterior}}</span>$ {{rangoElegido.precio}}</p>
                <!-- <p class="precio" style="text-align: center;" ><span v-if="oferta" class="precio-oferta">${{rangoElegido.precio_anterior}}</span>$ {{rangoElegido.precio}}</p> -->
            </div>
        </div>
    </div>
    <!-- <div v-if="rangos == '[]'">
        
            <div>
                <p class="precio" style="text-align: center;"><span v-if="oferta" class="precio-oferta">${{precioAnterior}}</span>$ {{price}}</p>
            </div>
        </div> -->

</div>



</template>

<script>
    import Swal from 'sweetalert2';
    export default {
        name: 'AddToCartButton',
        props: { 
            price: {type: Number},
            rangos: {},
            oferta: {type: Boolean},
            precioAnterior: {type: Number}
        },
        data() { // lo que tengo de mi componente, sus datos, tambien las globales con window
            return {
                rangos2: {},
                rangoElegido: null

            };
        },
        created() { // lo que aparece cuando se crea el componente
            
            
            this.rangos2 = JSON.parse(this.rangos)
            if(this.rangos2.length){

                this.rangoElegido = this.rangos2[0]
                this.price= rangoElegido.precio

            }else{

            }
        },
        methods: {
            selectRango(rango){
                this.rangoElegido = rango
                this.price= rango.precio
            },
            formatPrice(value) {
                let val = (value/1).toFixed(2).replace('.', ',')
                return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
            }
        },

        
    };
</script>

<style scoped>

    .rango {
        border: 1px solid #A6CE39;
        border-radius: 4px;
        font: normal normal 300 15px/18px Rubik;
        letter-spacing: 0px;
        color: #A6CE39;
        margin-right: 3;
        padding: 3 0 3 0;
        cursor:pointer;
        text-align: center;
        margin-bottom: 5px;
        width:47.5%;
    }


    .rango:hover, .rango.select{
        background: #A6CE39 0% 0% no-repeat padding-box;
        border: 1px solid #A6CE39;
        color: #FFFFFF;
    }




    .box-clase-mini{
        margin-top:22px;
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
        max-width: 95%;
        max-height: 95%;
        
        margin-right: calc(var(--bs-gutter-x)/ 2);
        margin-left: calc(var(--bs-gutter-x)/ 2);
        padding: 0;
    }

    .box-clase-mini .overlay{
        padding-bottom: 100%;
    }

    .box-clase-mini .overlay:hover{
        box-shadow: 0 0 0 9px #E9E9E9;
    }














    .add-to-cart-button {
        display: inline-block;
        padding: 0.4em 1em;
        border: none;
        font: inherit;
        font-size: 15px;
        text-transform: uppercase;
        color: #fff;
        background-color: #2f6410;
        cursor: pointer;
        transition: opacity 200ms ease;
    }
    .checkout-button {
        display: inline-block;
        padding: 0.4em 1em;
        border: none;
        font: inherit;
        font-size: 15px;
        text-transform: uppercase;
        border-top-right-radius: 25px;
        border-bottom-right-radius: 25px;
        color: #fff;
        background-color: #111282;
        cursor: pointer;
        transition: opacity 200ms ease;
    }

    .add-to-cart-button:hover {
        opacity: 0.75;
    }

    .col-3 {
        width: 23.5% !important;
    }
</style>