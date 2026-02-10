<template>

<div>

    <div v-if="diametros2.length >= 1">
        
        <p style="font: normal normal 300 14px/17px Rubik;">Diámetro</p>
        <div class="container-fluid" >
            <div class="row" >
                
                <!-- <div  v-show="diametro.show" :key="key"> -->

                    <div class="col-3 diametro" v-show="diametro.show" v-for="(diametro, key) in JSON.parse(diametros)" :key="key" :class=" diametro.id == diametroElegido.id  ? 'select' : '' "  @click="selectDiametro(diametro)"  > 
                        <span>{{diametro.tamano}}</span>
                        
                    </div>
                <!-- </div> -->

            </div> 

            <div v-if="diametroElegido">
                <p class="precio" style="text-align: center;"><span v-if="oferta" class="precio-oferta">${{diametroElegido.precio_anterior}}</span>$ {{diametroElegido.precio}}</p>
            </div>
        </div>
    </div>
    <div v-if="diametros == '[]'">
        
            <div>
                <p class="precio" style="text-align: center;"><span v-if="oferta" class="precio-oferta">${{precioAnterior}}</span>$ {{price}}</p>
            </div>
        </div>

</div>



</template>

<script>
    import Swal from 'sweetalert2';
    export default {
        name: 'AddToCartButton',
        props: { 
            price: {type: Number},
            diametros: {},
            oferta: {type: Boolean},
            precioAnterior: {type: Number}
        },
        data() { // lo que tengo de mi componente, sus datos, tambien las globales con window
            return {
                diametros2: {},
                diametroElegido: null

            };
        },
        created() { // lo que aparece cuando se crea el componente
            
            
            this.diametros2 = JSON.parse(this.diametros)
            if(this.diametros2.length){

                this.diametroElegido = this.diametros2[0]
                this.price= diametroElegido.precio

            }else{

            }
        },
        methods: {
            selectDiametro(diametro){
                this.diametroElegido = diametro
                this.price= diametro.precio
            },
            formatPrice(value) {
                let val = (value/1).toFixed(2).replace('.', ',')
                return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
            }
        },

        
    };
</script>

<style scoped>

    .diametro {
        border: 1px solid rgba(143, 134, 110, 0.3);
        border-radius: 4px;
        font: normal normal 300 15px/18px Rubik;
        letter-spacing: 0px;
        color: #8F866D;
        margin-right: 3;
        padding: 3 0 3 0;
        cursor:pointer;
        text-align: center;
        margin-bottom: 5px;
    }


    .diametro:hover, .diametro.select{
        background: #8F866D 0% 0% no-repeat padding-box;
        border: 1px solid #8F866E;
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