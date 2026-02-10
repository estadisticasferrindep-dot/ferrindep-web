
<template>
    <div class="row">

        <!-- 1. EMPTY CART STATE -->
        <template v-if="itemsCount === 0">
            <div class="col-12 text-center" style="padding: 80px 20px;">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" style="width: 70px; height: 70px; fill: #ddd; margin-bottom: 20px;">
                    <path d="M336 64h-80c0-35.3-28.7-64-64-64s-64 28.7-64 64H48C21.5 64 0 85.5 0 112v352c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48V112c0-26.5-21.5-48-48-48zM192 40c13.3 0 24 10.7 24 24s-10.7 24-24 24-24-10.7-24-24 10.7-24 24-24zm144 418c0 3.3-2.7 6-6 6H54c-3.3 0-6-2.7-6-6V118c0-3.3 2.7-6 6-6h42v24c0 13.3 10.7 24 24 24h144c13.3 0 24-10.7 24-24v-24h42c3.3 0 6 2.7 6 6v340zM269.7 178l-94.6 94.6-43.7-43.7c-4.7-4.7-12.3-4.7-17 0l-9.9 9.9c-4.7 4.7-4.7 12.3 0 17l62.1 62.1c4.7 4.7 12.3 4.7 17 0l103-103c4.7-4.7 4.7-12.3 0-17l-9.9-9.9c-4.7-4.7-12.3-4.7-17 0z"/>
                </svg>
                <h3 style="color: #666; font-weight: bold; margin-bottom: 10px;">Orden de Compra sin productos</h3>
                <a href="/" class="btn btn-primary" style="background-color: #FD914D; border-color: #FD914D; padding: 12px 30px; border-radius: 5px; font-weight: bold; color: white; text-decoration: none;">
                    VER PRODUCTOS
                </a>
            </div>
        </template>

        <!-- 2. CHECKOUT FLOW (Only if cart has items) -->
        <template v-else-if="step == 0">
            <div class="col-12 col-md-8">
                <cart-list :subtotal.sync="subtotal" @update-cart-count="count" @cart-updated="onCartUpdated" />
            </div>
            
            <div class="col-12 col-md-4">
                <checkout 
                    :ruta-productos="rutaProductos" 
                    :envios="envios"
                    :destinos="destinos"
                    :zonas="zonas"
                    :destinozonas="destinozonas"
                    :pesozonas="pesozonas"
                    :login="login"

                    :envio2.sync="envio"
                    :subtotal.sync="subtotal"
                    :target="target"
                    ref="checkout"
                    :parrafo-envio-fabrica="parrafoEnvioFabrica "
                    :parrafo-envio-interior="parrafoEnvioInterior "
                    :parrafo-envio-caba="parrafoEnvioCaba "
                    :parrafo-envio-expreso="parrafoEnvioExpreso "

                    :costo-envio-fabrica="costoEnvioFabrica "
                    :costo-envio-interior="costoEnvioInterior "
                    :costo-envio-caba.sync="costos"
                    :costo-envio-expreso="costoEnvioExpreso " 

                    :step.sync="step"
                    :total.sync="total"  :costoEnvio.sync="costoEnvio" :destinoId.sync="destinoId"
                    :ubicacion-preasignada="ubicacionPreasignada"
                    :cart="cart"
                />
            </div>
        </template>

        <!-- 3. FINALIZAR COMPRA (Only if cart has items) -->
        <finalizar-compra @back="goBack" :envio.sync="envio" v-if="step == 1 && itemsCount > 0" :total.sync="total"  :costoEnvio.sync="costoEnvio" :destinoId.sync="destinoId" :target="target" :destinos="destinos"
            :parrafo-efectivo="parrafoEfectivo"
            :parrafo-transferencia="parrafoTransferencia"
            :parrafo-mp="parrafoMp"
            :parrafo-envio-expreso="parrafoEnvioExpreso "
            :descuento-efectivo="descuentoEfectivo"
            :descuento-transferencia="descuentoTransferencia"
            :descuento-mp="descuentoMp"
            :costo-envio-fabrica="costoEnvioFabrica "
            :costo-envio-interior="costoEnvioInterior "
            :costo-envio-caba="costoEnvioCaba"
            :costo-envio-expreso="costoEnvioExpreso "

           :parrafo-envio-fabrica="parrafoEnvioFabrica "
            :parrafo-envio-interior="parrafoEnvioInterior "
            :parrafo-envio-caba="parrafoEnvioCaba "
            />
    </div>



</template>
<script>
    import CartList from './CartList.vue';
    import Checkout from './Checkout.vue';
    import FinalizarCompra from './FinalizarCompra.vue';



    export default {
        name: 'CartWidget',
        
        props:{
            rutaProductos: '',
            envios:{},

            login: { type: Number, default: 0 },
            destinos:{},
            zonas:{},
            destinozonas:{},
            pesozonas:{},

            img: {},
            target: {},
            parrafoEfectivo:{},
            parrafoTransferencia:{},
            parrafoMp:{},
            descuentoEfectivo:{},
            descuentoTransferencia:{},
            descuentoMp:{},


            parrafoEnvioFabrica: {type:String},
            parrafoEnvioInterior: {type:String},
            parrafoEnvioCaba: {type:String},
            parrafoEnvioExpreso: {type:String},

            costoEnvioFabrica: {type: Number},
            costoEnvioInterior: {type: Number},
            costoEnvioExpreso: {type: Number},
            costoEnvioCaba: {type: Number},
            ubicacionPreasignada: { type: Object, default: () => ({}) }
        },

        components:{
            'cart-list': CartList,
            'checkout': Checkout,
            'finalizar-compra': FinalizarCompra

        },
        data() {
            return {
                itemsCount: 0,
                cart: [],
                step:0,
                subtotal:0,
                total:0,
                envio:'',
                costoEnvio:0,
                destinoId:0,

                costos:{
                    envioCaba:this.costoEnvioCaba
                }
            };
        },
        watch:{
            step(val){
                if(val === 1){
                    window.history.pushState({step: 1}, "", "");
                }
            }
        },
        mounted() {
            if (this.ubicacionPreasignada && this.ubicacionPreasignada.cityName) {
                // Shipping session data is passed to the Checkout component via props.
                // Here we can assign to local state if needed.
            }
        },
        created() {
            this.count();
            window.addEventListener('popstate', this.handlePopState);
        },
        destroyed() {
            window.removeEventListener('popstate', this.handlePopState);
        },
        methods: {
            handlePopState(event) {
                // Si el usuario vuelve atrás y estamos en un paso > 0, volvemos al inicio
                if (this.step > 0) {
                    this.step = 0;
                }
            },
            goBack() {
                window.history.back();
            },
            count(newCount = null){
                if (typeof newCount === 'number') {
                    // Update via event payload (direct reactivity)
                    this.cart = window.getCartSafe(); // Refresh data just in case
                    this.itemsCount = newCount;
                } else {
                    // Fallback to reading storage
                    this.cart = window.getCartSafe();
                    this.itemsCount = this.cart.length;
                }
            },
            onCartUpdated(updatedCart) {
                // Re-read cart from localStorage to propagate changes to Checkout
                this.cart = Array.isArray(updatedCart) ? [...updatedCart] : window.getCartSafe();
                this.itemsCount = this.cart.length;
            }
        }
    }
</script>

<style>

.carrito {    
    position:relative;
    padding-left: 12px;
}

.carrito span{
    position: absolute;
    color:white;
    font: normal normal bold 10px/12px Rubik;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 50%;
    bottom: 4px;
    right: 10px;
    width: 17px;
    height: 17px;
    background-color:#8F866D;
    padding:1px;
    cursor: pointer; 
}


.carrito i{
    width: 14px !important;
    height: 13px !important;
    color: #EEEEEE;
    font-size: 16px;
    cursor: pointer;
}

    @media screen and (max-width: 800px) {
        
        img{
            height: 39px !important;
            margin-top:0 !important;
        }
    } 

        
</style>