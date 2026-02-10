<template>
    <div>
    <h4 class="d-flex d-md-none" style="    font: 600 16px / 28px 'Open Sans'; margin-top:20px"> Detalle de su orden de compra:</h4>
    <table class="table" style=" border: 0.5px solid rgba(143, 134, 110, 0.3) ;     margin-top: 20px;">
        <thead class="d-none d-md-table-header-group"  style="">
            <tr style="font: normal normal normal 24px/28px Open Sans; font-weight:500">
                <td class="table-header" style=" border: 0.5px solid rgba(143, 134, 110, 0.3) !important;  padding:12px 22px 10px 22px;" >
                    Orden de compra
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td class="d-none d-md-table-cell"></td>
            </tr>
        </thead>  

        <tbody>
            <tr v-for="(itemLista, key) in cart" :key="key" >
                <td scope="row"  style="display:flex; flex-direction:column; align-items:center; justify-content:center;" >  

                    <div class="imagen-carrito-mobile" :style="'border: 1px solid #CCCCCC; width:98px; height:103px; background-image: url('+ itemLista.imagen + ');background-repeat: no-repeat;background-position: center;background-size: contain;'" ></div>
                    <div class="borrar d-flex d-md-none" style="margin-bottom: 5px; cursor: pointer;" @click="borrarItem(key)">
    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#999999" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="3 6 5 6 21 6"></polyline>
        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
        <line x1="10" y1="11" x2="10" y2="17"></line>
        <line x1="14" y1="11" x2="14" y2="17"></line>
    </svg>
</div>
                
                </td>    
                <td class="nombre">
                     <h3 v-if="itemLista.conNombre" >{{itemLista.nombre}} ({{itemLista.ancho}})</h3>

                     <div v-else>
                        <div class="prod-title">{{itemLista.medidas}} {{itemLista.espesor}}</div>
                        
                        <div class="prod-meta" v-if="itemLista.ancho >= 100 ">
                            {{itemLista.ancho/100}} m alto/ancho <span v-if="itemLista.metros > 1">({{itemLista.metros}}m)</span>
                        </div>
                        <div class="prod-meta" v-else>
                            {{itemLista.ancho}} cm alto/ancho <span v-if="itemLista.metros > 1">({{itemLista.metros}}m)</span>
                        </div>
                     </div>
                    <div v-if="itemLista.free" style="font-size: 13px; color: green; font-weight:bold"> (Envío gratis)</div>
                </td>
                <td class="cantidad">
                    <!-- Desktop -->
                    <input class="d-none d-md-block" type="number" min="1" v-model="itemLista.cantidad" :max="Math.min(Math.floor(itemLista.limite),itemLista.stock)">
                    <!-- Mobile Stepper -->
                    <div class="d-flex d-md-none stepper-ui">
                        <button class="step-btn minus" @click="decrement(itemLista)">-</button>
                        <input type="number" readonly v-model="itemLista.cantidad" class="step-input">
                        <button class="step-btn plus" @click="increment(itemLista)">+</button>
                    </div>
                </td>
                <td class="total d-none d-md-table-cell">$ {{itemLista.precio * itemLista.cantidad | toCurrency}}</td>
                <td class="total d-table-cell d-md-none">$ {{itemLista.precio * itemLista.cantidad | toCurrency}}</td>

                <td class="borrar d-none d-md-table-cell" style="cursor: pointer;" @click="borrarItem(key)">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#999999" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2">
        <polyline points="3 6 5 6 21 6"></polyline>
        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
        <line x1="10" y1="11" x2="10" y2="17"></line>
        <line x1="14" y1="11" x2="14" y2="17"></line>
    </svg>
</td>
            </tr>
        </tbody>
    </table> 
    </div>               
</template>
<script>
    export default {

        props:{
            subtotal: {}
        },
        data() {
            return {
                cart: [],
                cargado: false
            }
        },
        watch:{
            cart: {
                handler(val){
                    if(this.cargado){
                        // this.redondear_cantidades(this.cart)
                        localStorage.setItem("cartQunuy",JSON.stringify(this.cart));
                        this.actualizar_subtotal(this.cart);
                        // Sync quantities to server session cart for accurate shipping calculations
                        this.syncSessionCart(this.cart);
                        // Notify parent that cart data changed (for shipping recalculation)
                        this.$emit('cart-updated', this.cart);
                    }
                },
                deep: true
            }
        },
        created() {
            try {
                const raw = localStorage.getItem('cartQunuy');
                const parsed = raw ? JSON.parse(raw) : [];
                this.cart = Array.isArray(parsed) ? parsed : [];
            } catch (e) {
                localStorage.removeItem('cartQunuy');
                this.cart = [];
            }


            axios.get(window.publicPath()+'/cartdata', {
                params: {
                    cart: JSON.stringify( this.cart),
                }
            })
            .then((response) => {
                this.cart = Array.isArray(response.data) ? response.data : [];
                localStorage.setItem("cartQunuy", JSON.stringify(this.cart));
                this.cargado = true;
                this.actualizar_subtotal(this.cart);
                this.$root.$emit('count','data');
            })


            // for (let index = 0; index < this.cart.length; index++) {
            //     item = this.cart[index]
                
            //     if (item.cantidad > item.limite || item.cantidad > item.stock) { 
            //         item.cantidad = Math.min(item.limite,item.stock)
            //     }
            // }
            // localStorage.setItem("cartQunuy",JSON.stringify(this.cart));
            // this.cart = JSON.parse(localStorage.getItem('cartQunuy'));
            

            this.cargado = true;
        },
        methods: {
            redondear_cantidades(cartActualizado){

                for (let index = 0; index < cartActualizado.length; index++) {
                    let item = cartActualizado[index]

                    if (item.cantidad > 0) {
                        item.cantidad = Math.round(item.cantidad)
                        
                    }
                    
                }
            },
            increment(item) {
                let limit = Math.min(Math.floor(item.limite), item.stock);
                if (item.cantidad < limit) {
                    item.cantidad++;
                }
            },
            decrement(item) {
                if (item.cantidad > 1) {
                    item.cantidad--;
                }
            },
            borrarItem(key){
                let removed = this.cart[key];
                this.cart.splice(key, 1)
                
                // CRITICAL FIX: Save immediately before component potentially unmounts
                localStorage.setItem("cartQunuy", JSON.stringify(this.cart));
                
                // Sync deletion to server session (set qty=0)
                if (removed && removed.presentacionId) {
                    this.syncItemToSession(removed.presentacionId, 0);
                }
                
                this.$root.$emit('seAnulaEnvio','data');
                this.$root.$emit('count','data');
                this.$emit('update-cart-count', this.cart.length); // FIX: Send exact length to avoid sync issues

            },
            // elegirRango(item){
            //     let cant = item.cantidad
            //     let rang = item.rangos

            //     for (let index = 0; index < rang.length; index++) {
            //         if(index != rang.length -1){
            //             if (rang[rang.length-2-index].max < cant){
            //                 return rang[rang.length-1-index]
            //             }
            //         }
            //         else{
            //             return rang[0]
            //         }
            //     }
            // },
            actualizar_subtotal(cartActualizado){
                let subtotal = 0
                
                this.precios = []
                // cartActualizado.forEach((item) => {
                //                 this.precios.push(item.price * item.cantidad);
                //             });

                // cartActualizado.forEach((item) => {
                //                 this.precios.push(this.elegirRango(item).precio * item.cantidad);
                //                 console.log(this.elegirRango(item).precio)
                //             });

                cartActualizado.forEach((item) => {
                                this.precios.push(item.precio * item.cantidad);
                            });

                for (var i = 0; i < this.precios.length; i++){			
                    subtotal +=  this.precios[i];
                }

                this.$emit('update:subtotal',subtotal)
                // this.calculo_iva()
                // this.calculo_descuento_pago()
                // this.calculo_envio()
                // this.calculo_total()
            },
            toCart(){
                window.location.replace(this.ruta);
            },
            /**
             * Sync all cart items to the server session cart.
             * Debounced to avoid hammering the server on rapid quantity changes.
             */
            syncSessionCart(cart) {
                if (this._syncTimeout) clearTimeout(this._syncTimeout);
                this._syncTimeout = setTimeout(() => {
                    const csrfToken = document.head.querySelector('meta[name="csrf-token"]')?.content;
                    if (!csrfToken) return;
                    
                    cart.forEach(item => {
                        if (item.presentacionId) {
                            this.syncItemToSession(item.presentacionId, parseInt(item.cantidad) || 0);
                        }
                    });
                }, 500); // 500ms debounce
            },
            /**
             * Sync a single item's quantity to the server session cart.
             */
            syncItemToSession(presentacionId, qty) {
                const csrfToken = document.head.querySelector('meta[name="csrf-token"]')?.content;
                if (!csrfToken) return;
                
                fetch('/carrito/session/set-qty', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ presentacion_id: presentacionId, qty: qty })
                }).catch(() => { /* Non-blocking */ });
            }
        }

    }
</script>

<style lang="scss" scoped>

    .table>:not(caption)>*>* {
        border:none;
    }

    .section-carrito tbody .btn-x{
        width: 20px;
        height: 20px;
        background-color:transparent;
        margin-right:21px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor:pointer;
        font-size:19px;
        color: #FD914D;
    }

    .cantidad input{
        border: 1px solid rgba(143, 134, 110, 0.3);
        width: 81px;
        border-radius: 10px;
        text-align: left;
        font: normal normal normal 14px/23px Open Sans;
        color: #8F866D;
        padding:6px;
        padding-left:15px;
    }

    .table-header{
        column-span: all;
    }

    
    .section-carrito tbody td{
        text-align: left;
        letter-spacing: 0px;
        padding:13px;
        vertical-align: middle !important;
    }


    @media screen and (max-width: 800px) {
        .cantidad input{
            width: 50px;
        }

        .section-carrito tbody .cantidad{
            padding-left: 0;
            padding-right: 0;
            display: flex;
            justify-content: center;
        }
        
        .section-carrito tbody .total{
            padding-right: 0;
        }

        .section-carrito tbody .nombre{          
            width: 117px;
        }

        .section-carrito tbody .borrar{
            padding-right: 0;
        }

        .section-carrito tbody td{
            padding: 0;
        }

        .imagen-carrito-mobile{
            border:none !important;
            width: 56px !important;
            height: 86px !important;
        }

        /* Removed old h3/h4 rules, replaced with: */
        .prod-title {
            font-size: 13px !important;
            font-weight: bold;
            color: #333;
            line-height: 1.2;
            margin-bottom: 2px;
        }
        .prod-meta {
            font-size: 12px !important;
            color: #777;
            line-height: normal;
        }

        table{
            margin:0 !important;
            border:none !important;
        }

        tr{
            border: none !important;
            border-bottom: 0.5px solid rgba(143, 134, 110, 0.3) !important;
            border-top: 0.5px solid rgba(143, 134, 110, 0.3) !important;
            
            /* Grid Layout for Mobile Row - 3 Columns */
            display: grid;
            grid-template-columns: min-content 1fr min-content; /* Image | Stepper | Price */
            grid-template-rows: auto auto;
            grid-column-gap: 10px;
            padding: 10px 0;
            position: relative; 
            align-items: center; /* Vertically align items */
        }

        /* Image Cell (Left, spans 2 rows) */
        .section-carrito tbody td:first-child { 
            grid-row: 1 / 3; 
            grid-column: 1;
            width: auto;
            padding-right: 5px;
        }

        /* Name Cell (Top, spans 2 columns) */
        .section-carrito tbody .nombre {
            grid-row: 1;
            grid-column: 2 / 4; /* Span across stepper and price cols */
            width: auto !important; 
            margin-bottom: 5px;
        }

        /* Quantity Cell (Bottom Left) */
        .section-carrito tbody .cantidad {
            grid-row: 2;
            grid-column: 2;
            justify-content: flex-start !important;
            padding: 0;
            margin: 0;
        }

        /* Price Cell (Bottom Right) - Target ONLY the mobile price element */
        .section-carrito tbody .total.d-md-none {
            grid-row: 2;
            grid-column: 3;
            text-align: right;
            padding: 0;
            margin: 0;
            
            /* Mobile Price Style */
            font-size: 16px !important; 
            white-space: nowrap;
            display: block !important;
            position: static; /* Remove absolute positioning */
        }

        /* Stepper UI Adjustments (Smaller) */
        .stepper-ui {
            height: 26px; /* Reduced from 30px */
            width: auto;
            margin: 0; /* Remove auto margin */
        }
        .step-btn {
            width: 24px; /* Reduced from 28px */
            font-size: 16px;
        }
        .step-input {
            width: 30px !important; /* Reduced from 34px */
            font-size: 13px;
        }
        .stepper-ui {
            display: flex;
            align-items: center;
            border: 1px solid #ccc;
            border-radius: 6px;
            overflow: hidden;
            height: 30px;
            width: auto;
            margin: 0 auto;
        }
        .step-btn {
            background: #fff;
            border: none;
            width: 28px;
            height: 100%;
            font-size: 18px;
            font-weight: bold;
            color: #FD914D;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            cursor: pointer;
        }
        .step-btn:active {
            background: #eee;
        }
        .step-input {
            width: 34px !important;
            text-align: center;
            border: none !important;
            border-left: 1px solid #eee !important;
            border-right: 1px solid #eee !important;
            font-size: 14px;
            height: 100%;
            pointer-events: none;
            background: white;
            padding: 0 !important;
            color: #333 !important;
        }

    }



    @media screen and (min-width: 768px){
        .d-md-table-header-group {
                display: table-header-group !important;
        }
    }
    

    .section-carrito tbody input:focus-visible{

        outline:0 !important;
    }

    .total{
        font: normal normal bold 25px/34px Open Sans;
        color: #FD914D;
        white-space: nowrap;
        font-size:18px;
    }

    td h3{
        font: normal normal normal 20px/24px Open Sans;
    }

    td h4{
        font: normal normal normal 13px/15px Open Sans;
    }


    .section-carrito thead th{
        letter-spacing: 0.75px;
        text-align:right;
        padding:19px;
    }


    tr{
        border: 0.5px solid rgba(143, 134, 110, 0.3);
    }
</style>