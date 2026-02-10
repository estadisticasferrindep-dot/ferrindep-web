<template>
    <div class="container mt-mobile-fix" style="margin-top:87px; margin-bottom: 93px; position: relative;">

        <div v-if="cargando" class="loading-overlay">
            <div class="spinner"></div>
            <p style="margin-top: 15px; color: white; font-weight: bold; font-size: 18px;">Procesando pedido...</p>
        </div>

        <div class="row flex-column-reverse flex-md-row">
            
            <!-- COLUMNA IZQUIERDA: FORMULARIO WIZARD -->
            <div class="col-12 col-md-8" style="margin-bottom:50px;">
                <div style="padding:12px 22px 10px 22px; border-bottom: 0.5px solid rgba(143, 134, 110, 0.3); width:100%">
                    <!-- Indicador de progreso -->
                    <ol class="steps">
                        <li :class="{ active: currentStep === 1 }">Datos personales</li>
                        <li :class="{ active: currentStep === 2 }" v-show="envio !== 'fabrica'">Dirección de entrega</li>
                        <li :class="{ active: currentStep === 3 }">Revisión y pago</li>
                    </ol>
                </div>
                
                <div class="formulario" style="padding: 15px;">
                    
                    <!-- STEP 1: DATOS PERSONALES -->
                    <!-- STEP 1: DATOS PERSONALES -->
                    <div v-show="currentStep === 1">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <input class="input-carrito" name="nombre" v-model="nombre" placeholder="Nombre y Apellido">                                    
                            </div>
                            <div class="col-12 col-md-6">
                                <input class="input-carrito" name="email" v-model="email" placeholder="Email">                                    
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <input class="input-carrito" name="celular" v-model="celular" placeholder="Celular">                                    
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" style="width: auto; background: #999; border-color: #999; padding-left: 20px; padding-right: 20px;" @click="prevStep">ATRÁS</button>
                            <button type="button" style="width: auto; padding-left: 30px; padding-right: 30px;" @click="nextStep">SIGUIENTE</button>
                        </div>
                    </div>

                    <!-- STEP 2: DIRECCIÓN (CON GOOGLE MAPS) -->
                    <!-- STEP 2: DIRECCIÓN (CON GOOGLE MAPS) -->
                    <div v-show="currentStep === 2">
                        <div class="row">
                            <div class="col-12">
                                <label style="font-size: 13px; color: #666; display:block; margin-top:20px;">Dirección / Calle y Altura</label>
                                <input ref="addressInputDesktop" class="input-carrito" style="margin-top: 5px;" name="direccion" v-model="direccion" placeholder="Ej: Av. Rivadavia 1234, CABA">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <input class="input-carrito" name="localidad" v-model="localidad" placeholder="Localidad">                                    
                            </div>
                            <div class="col-12 col-md-6">
                                <input class="input-carrito" name="provincia" v-model="provincia" placeholder="Provincia">                                    
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <input class="input-carrito" name="cp" v-model="cp" placeholder="Código Postal">                                    
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" style="width: auto; background: #999; border-color: #999; padding-left: 20px; padding-right: 20px;" @click="prevStep">ATRÁS</button>
                            <button type="button" style="width: auto; padding-left: 30px; padding-right: 30px;" @click="nextStep">SIGUIENTE</button>
                        </div>
                    </div>

                    <!-- STEP 3: MÁS DATOS Y CONFIRMACIÓN -->
                    <!-- STEP 3: MÁS DATOS Y CONFIRMACIÓN -->
                    <div v-show="currentStep === 3">
                        <div class="row"> 
                            <div class="col-12">
                                <div v-if="pago === 'transferencia'" style="margin-top: 10px; padding: 8px; background-color: #f9f9f9; border-radius: 8px;">
                                    <div style="display: flex; align-items: center;">
                                        <input type="checkbox" id="facturaA" v-model="requiereFacturaA" style="width: 16px; height: 16px; margin: 0; margin-right: 8px; cursor: pointer;">
                                        <label for="facturaA" style="margin: 0; font-size: 13px; font-weight: normal; cursor: pointer; line-height: 1.2;">
                                            Requiero factura previamente para emitir el pago por transferencia
                                        </label>
                                    </div>
                                    
                                    <div v-if="requiereFacturaA" style="margin-top: 8px; margin-left: 24px;">
                                        <input class="input-carrito" style="margin-top:0; padding: 5px; font-size: 13px;" name="dni" v-model="dni" placeholder="Ingrese CUIT">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Opciones de Pago (Card UI) -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h6 style="font-weight: bold; margin-bottom: 15px;">Seleccione método de pago:</h6>
                                
                                <div class="payment-card" :class="{ active: pago === 'transferencia' }" @click="pago = 'transferencia'">
                                    <input type="radio" value="transferencia" v-model="pago" style="display:none">
                                    <div class="d-flex align-items-center">
                                        <div class="payment-circle"></div>
                                        <span>Transferencia / Depósito</span>
                                    </div>
                                </div>

                                <div class="payment-card" :class="{ active: pago === 'efectivo' }" @click="pago = 'efectivo'" v-if="envio == 'fabrica'">
                                    <input type="radio" value="efectivo" v-model="pago" style="display:none">
                                    <div class="d-flex align-items-center">
                                        <div class="payment-circle"></div>
                                        <span>Efectivo (Solo retiro en local)</span>
                                    </div>
                                </div>

                                <div class="payment-card" :class="{ active: pago === 'mp' }" @click="pago = 'mp'">
                                    <input type="radio" value="mp" v-model="pago" style="display:none">
                                    <div class="d-flex align-items-center">
                                        <div class="payment-circle"></div>
                                        <span>MercadoPago</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4"> 
                            <div class="col-12">
                                <textarea name="mensaje" v-model="mensaje" rows="5" placeholder="Notas del pedido / referencias / aclaraciones respecto a la entrega "></textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4 align-items-center">
                            <button type="button" style="width: auto; background: #999; border-color: #999; padding-left: 20px; padding-right: 20px;" @click="prevStep">ATRÁS</button>
                            
                            <button class="d-block d-md-none"
                                :disabled="cargando || !(total2 > 0)" 
                                @click="enviar"
                                style="width: auto; border: none; padding: 10px 20px; border-radius: 5px; color: white; font-weight: bold;"
                                :style="{ background: (cargando || !(total2 > 0)) ? '#ccc' : '#FD914D', cursor: (cargando || !(total2 > 0)) ? 'not-allowed' : 'pointer' }">
                                {{ cargando ? 'PROCESANDO...' : 'REALIZAR PEDIDO' }}
                            </button>
                        </div>
                    </div>

                </div>
            </div>

            <!-- COLUMNA DERECHA: RESUMEN DE COMPRA -->
            <div class="col-12 col-md-4">
                <div style="border: 0.5px solid rgba(143, 134, 110, 0.3);">
                    <div class="section-envio">
                        <!-- LISTADO DE PRODUCTOS (Resumen) -->
                        <div v-if="cart && cart.length > 0" class="mt-3 mb-3">
                            <h5 style="margin-bottom: 10px;">Resumen</h5>
                            <div v-for="item in cart" :key="item.id" style="font-size: 13px; margin-bottom: 8px; line-height: 1.2;">
                                <div class="d-flex justify-content-between align-items-baseline">
                                    <div style="font-weight: 600; font-size: 13px; color: #333; padding-right: 10px;">
                                        <span style="color: #F37021; font-size: 11px;">{{ item.cantidad }} -</span> {{ item.familia || item.nombre }}
                                    </div>
                                    <div style="font-weight: 600; font-size: 13px; color: #333; white-space: nowrap;">
                                        $ {{ (parseFloat(item.price || item.precio) * parseFloat(item.cantidad || item.quantity)) | toCurrency }}
                                    </div>
                                </div>
                                <div style="margin-top: 2px; line-height: 1;">
                                    <span style="font-size: 11px; color: #555; font-weight: normal;">{{ item.medidas }}</span>
                                    <span v-if="item.ancho" style="font-size: 11px; color: #555; font-weight: normal;"> | {{ item.ancho }}cm</span> 
                                    <span v-if="item.espesor" style="font-size: 11px; color: #555; font-weight: normal;"> | {{ item.espesor }}</span>
                                    <span v-if="item.metros" style="font-size: 11px; color: #555; font-weight: normal;"> ({{ parseFloat(item.metros) }}m)</span>
                                </div>
                            </div>
                            <hr>
                        </div>

                        <div v-if="envio=='fabrica'" class="fila">
                                <h5>Retiro en depósito</h5> 
                                <span  v-if=" costoEnvioFabrica!='0' && costoEnvioFabrica!='-1'" style=" margin-left:15px;">$ {{ costoEnvioFabrica}}</span>
                        </div>

                        <div v-if="envio=='caba'" class="fila">
                                <h5>Envío </h5> 
                                <span  v-if=" costoEnvio!=0 " style=" margin-left:15px; color:#333; font-weight:600; font-size:13px;">$ {{costoEnvio}}</span> <span v-else >Gratis</span>
                        </div>
                        <div v-if="envio=='caba'">
                            <p v-html="nombreDestino" style="font-size: 13px; color: #333; margin-top: 5px;"></p>
                        </div>

                        <div class="fila" style="padding-bottom:6px; padding-top:6px;"> 
                            <p class="total" style="margin:0">Total</p>
                            <span class="precio" style="text-align: right;">$ {{total2 | toCurrency}}</span>
                        </div>
                        <div v-if="descuento_total != 0" class="fila" style="margin-top: 5px;">
                            <span v-if="descuento_total > 0" style="color: green; font-size: 13px;">Descuento Pago</span>
                            <span v-else style="color: #F37021; font-size: 13px;">Recargo MercadoPago</span>
                            <span style="font-weight: bold; font-size: 13px;">$ {{ Math.abs(descuento_total) | toCurrency }}</span>
                        </div>
                        <hr>
                        
                        <!-- BOTÓN EXTRA SIEMPRE VISIBLE EN DESKTOP, SOLO ENABLED EN STEP 3 -->
                        <button class="d-none d-md-block" 
                            :disabled="cargando || currentStep < 3" 
                            @click="enviar"
                            style="opacity: 1;"
                            :style="{ background: currentStep < 3 ? '#ccc' : '#FD914D', borderColor: currentStep < 3 ? '#ccc' : '#FD914D', cursor: currentStep < 3 ? 'not-allowed' : 'pointer' }">
                            {{ cargando ? 'PROCESANDO...' : 'REALIZAR PEDIDO' }}
                        </button>
                    </div> 
                </div>      
            </div>

        </div>
    </div>
</template>

<script>


    import Swal from 'sweetalert2';

    export default {
        props:{
            target: {},
            parrafoEfectivo: {type: String},
            parrafoTransferencia: {type: String},
            parrafoMp: {type: String},
            parrafoEnvioExpreso: {type:String},
            descuentoEfectivo: {type: Number},
            descuentoTransferencia: {type: Number},
            descuentoMp: {type: Number},
            costoEnvioFabrica: {type: Number},
            costoEnvioInterior: {type: Number},
            costoEnvioCaba: {type: Number},
            costoEnvioExpreso: {type: Number},
            costoEnvio: {type: Number},
            destinoId: {},
            parrafoEnvioFabrica: {type:String},
            parrafoEnvioInterior: {type:String},
            parrafoEnvioCaba: {type:String},
            total:{},
            envio:{},
            destinos: {}
        },
        data() {
            return {
                cargando: false,
                currentStep: 1, // Nuevo: Control de pasos (1, 2, 3)
                requiereFacturaA: false, // Nuevo: Checkbox Factura A
                
                subtotal: 0,
                pago: 'transferencia',
                nombre:'',
                dni:'',
                email:'',
                celular:'',
                direccion:'',
                localidad:'',
                provincia:'',
                cp:'',
                mensaje:'',
                cart: [],
                precios: [],
                descuento_total: 0,
                envio_total: 0,
                total2: 0,
                
                autocomplete: null,
                autocompleteMobile: null
            }
        },
        watch: {
            currentStep(val) {
                if (val === 2) {
                    this.$nextTick(() => {
                        this.initGoogleMaps();
                    });
                }
            },
            pago(val) {
                this.calculo_descuento_pago();
                this.calculo_total();
            }
        },
        computed: {
            nombreDestino() {
                if (!this.destinos || !this.destinoId) return '';
                
                // Si destinos es un array
                if (Array.isArray(this.destinos)) {
                    const found = this.destinos.find(d => d.id == this.destinoId);
                    return found ? found.nombre : this.destinoId;
                }
                
                // Si es objeto (key mapped)
                if (this.destinos[this.destinoId]) {
                    return this.destinos[this.destinoId].nombre || this.destinos[this.destinoId];
                }

                return this.destinoId;
            }
        },
        created() {
            this.calculo_descuento_pago()
            this.calculo_total()
            this.cart = JSON.parse(localStorage.getItem('cartQunuy'));
            this.subtotal = this.total
        },
        mounted() {
            // Intentamos inicializar maps si ya estamos en paso 2 (raro, pero por si acaso)
            if (this.currentStep === 2) {
                this.initGoogleMaps();
            }
        },
        methods: {
            initGoogleMaps() {
                if (typeof google === 'undefined' || !google.maps || !google.maps.places) {
                    console.warn('Google Maps API not loaded');
                    return;
                }

                const options = {
                    componentRestrictions: { country: "ar" },
                    fields: ["address_components", "geometry", "icon", "name"],
                    strictBounds: false,
                };

                // Desktop Input
                const inputDesktop = this.$refs.addressInputDesktop;
                if (inputDesktop) {
                    this.autocomplete = new google.maps.places.Autocomplete(inputDesktop, options);
                    this.autocomplete.addListener("place_changed", () => {
                        this.fillAddress(this.autocomplete);
                    });
                }

                // Mobile Input
                const inputMobile = this.$refs.addressInputMobile;
                if (inputMobile) {
                    this.autocompleteMobile = new google.maps.places.Autocomplete(inputMobile, options);
                    this.autocompleteMobile.addListener("place_changed", () => {
                        this.fillAddress(this.autocompleteMobile);
                    });
                }
            },
            fillAddress(autocompleteParams) {
                const place = autocompleteParams.getPlace();
                if (!place.geometry) return;

                // Intentamos parsear la dirección
                // Nota: Esto es básico, se puede mejorar según necesidad
                this.direccion = place.name; // O formatted_address

                // Intentar sacar localidad/provincia/cp
                // (Simplificado, muchas veces Google retorna distinto)
                if (place.address_components) {
                    for (const component of place.address_components) {
                        const types = component.types;
                        if (types.includes('locality')) {
                            this.localidad = component.long_name;
                        }
                        if (types.includes('administrative_area_level_1')) {
                            this.provincia = component.long_name;
                        }
                        if (types.includes('postal_code')) {
                            this.cp = component.long_name;
                        }
                    }
                }
            },
            nextStep() {
                // DEBUG: Alert removido
                // alert('DEBUG: ...'); 
                
                if (this.currentStep === 1) {
                    let missing = [];
                    if (!this.nombre) missing.push('Nombre');
                    if (!this.email) missing.push('Email');
                    if (!this.celular) missing.push('Celular');

                    if (missing.length > 0) {
                        Swal.fire('Atención', 'Por favor completá: ' + missing.join(', '), 'warning');
                        return;
                    }

                    if (this.envio === 'fabrica') {
                        this.currentStep = 3;
                    } else {
                        this.currentStep = 2;
                    }
                    window.scrollTo(0, 0);
                } else if (this.currentStep === 2) {
                    let missing = [];
                    if (!this.direccion) missing.push('Dirección');
                    if (!this.localidad) missing.push('Localidad');
                    if (!this.provincia) missing.push('Provincia');
                    if (!this.cp) missing.push('CP');

                    if (missing.length > 0) {
                        Swal.fire('Atención', 'Por favor completá: ' + missing.join(', '), 'warning');
                        return;
                    }
                    this.currentStep = 3;
                    window.scrollTo(0, 0);
                }
            },
            prevStep() {
                if (this.currentStep > 1) {
                    if (this.currentStep === 3 && this.envio === 'fabrica') {
                        this.currentStep = 1;
                    } else {
                        this.currentStep--;
                    }
                    window.scrollTo(0, 0);
                } else {
                    this.$emit('back');
                }
            },

            calculo_subtotal(){
                this.subtotal = 0
                this.cart = JSON.parse(localStorage.getItem('cartQunuy'));
                
                this.precios = []
                this.cart.forEach((item) => {
                    this.precios.push((item.precio || item.price) * (item.cantidad || item.quantity));
                });
                
                for (var i = 0; i < this.precios.length; i++){			
                    this.subtotal = this.subtotal + this.precios[i];
                }
            },
            calculo_descuento_pago(){
                if(this.pago == 'efectivo'){
                    this.descuento_total = this.total * (this.descuentoEfectivo / 100)
                }
                else{
                    if(this.pago == 'transferencia'){
                        this.descuento_total = this.total * (this.descuentoTransferencia / 100)
                    }
                    else if(this.pago == 'mp'){
                        this.descuento_total = this.total * (-this.descuentoMp / 100)
                    }
                    else {
                        this.descuento_total = 0;
                    }
                }
            },
            calculo_total(){
                this.total2 = parseInt(this.total) - parseInt(this.descuento_total)
            },
            enviar(){
                if (this.cargando) return;

                // Validación final (Step 3 + general)
                // Si es envio a fabrica, NO validamos dirección
                if (this.envio !== 'fabrica') {
                     if (!this.direccion || !this.localidad || !this.provincia || !this.cp) {
                        Swal.fire('Atención', 'Faltan datos de entrega. Por favor revisá el paso 2.', 'warning');
                        this.currentStep = 2;
                        return;
                    }
                }

                if (this.requiereFacturaA && !this.dni) {
                    Swal.fire('Atención', 'Si requerís Factura A, el DNI/CUIT es obligatorio.', 'warning');
                    return;
                }

                if(!(this.total2 > 0)){
                    Swal.fire('Cuidado!', 'No ha seleccionado ningún producto para la compra', 'warning');
                    return;
                }

                this.cargando = true;

                if (this.requiereFacturaA) {
                    this.mensaje = "🔴 Requiere factura previamente para emitir el pago por transferencia \n" + (this.mensaje ? this.mensaje : '');
                }

                this.cart = JSON.parse(localStorage.getItem('cartQunuy'));
                var form = new FormData();
                let stringCart = JSON.stringify(this.cart)

                form.append('subtotal', this.subtotal);
                form.append('envio', this.envio);
                form.append('pago', this.pago);

                form.append('nombre', this.nombre);
                form.append('dni', this.dni); // Puede ir vacío si no pide Factura A
                form.append('email', this.email);
                form.append('celular', this.celular);
                form.append('direccion', this.direccion);
                form.append('localidad', this.localidad);
                form.append('provincia', this.provincia);
                form.append('cp', this.cp);
                form.append('mensaje', this.mensaje);
                form.append('localidad_envio', this.nombreDestino);

                form.append('stringCart', stringCart);
                form.append('precios', this.precios);
                
                form.append('descuento_total', this.descuento_total);
                form.append('envio_costo', this.costoEnvio || 0);
                form.append('total2', this.total2);
                
                localStorage.removeItem("cartQunuy");
                
                axios.post(this.target, form).then((response) => {
                    window.location = response.data.redirect
                }).catch(error=> {
                    this.cargando = false;
                    Swal.fire('Error', 'Hubo un problema al procesar el pedido. Intente nuevamente.', 'error');
                })
            }
        }
    }
</script>

<style lang="scss" scoped>

    /* ESTILOS DEL SPINNER */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7); /* Fondo negro semitransparente */
        z-index: 9999;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .spinner {
        width: 50px;
        height: 50px;
        border: 5px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: #FD914D; /* Naranja Ferrindep */
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* ESTILOS ORIGINALES */
    .section-envio{
        padding:0px 22px 39px 22px;
    }

    /* ESTILOS STEPS */
    .steps {
        display: flex;
        list-style: none;
        margin-bottom: 1rem;
        padding: 0;
        justify-content: space-between;
    }
    .steps li {
        flex: 1;
        text-align: center;
        padding: 0.5rem;
        border-bottom: 2px solid #ccc;
        color: #999;
        font-size: 14px;
    }
    .steps li.active {
        font-weight: bold;
        border-bottom-color: #fd914d;
        color: #333;
    }

    h3{
        font: normal normal bold 24px/28px Open Sans;
        margin-bottom:0;
    }

    .section-envio h4{
        font: normal normal bold 20px/24px Open Sans;
        margin-bottom:0px;
    }

    .section-envio .total {
        font: normal normal bold 20px/24px Open Sans;
    }

    .section-envio label{
        font: normal normal normal 20px/24px Open Sans;
    }

    .section-envio span{
        font: normal normal bold 19px/23px Open Sans;
        color: #FD914D;
    }

    .section-envio .precio{
        font: normal normal bold 22px/27px Open Sans;
        color: #FD914D;
    }

    hr{
        border: 0.5px solid rgba(143, 134, 110, 0.3);
    }

    .fila{
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top:13px;
    }

    button{
        text-align: center;
        font: normal normal bold 14px/17px Open Sans;
        letter-spacing: 0.56px;
        color: #FFFFFF;
        background: #FD914D 0% 0% no-repeat padding-box;
        border: 1px solid #FD914D;
        border-radius: 8px;
        padding-top: 11px;
        padding-bottom: 11px;
        width: 100%;
        transition: background 0.3s;
    }
    
    button:disabled {
        background: #ccc;
        border-color: #ccc;
        cursor: not-allowed;
    }

    .formulario input, textarea{
        border: 0.5px solid rgba(143, 134, 110, 0.3);
        border-radius: 8px;
        font: normal normal 300 13px/15px Open Sans;
        margin-top:27px;
        padding: 12px 15px 12px 15px;
        width:100%;
    }

    /* Estilos Custom para Payment Cards */
    .payment-card {
        padding: 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        background-color: #fff;
    }
    .payment-card:hover {
        border-color: #FD914D;
        background-color: #fffaf5;
    }
    .payment-card.active {
        border: 1px solid #FD914D;
        background-color: #fff8f0;
    }
    .payment-card .payment-circle {
        width: 18px;
        height: 18px;
        border: 2px solid #ccc;
        border-radius: 50%;
        margin-right: 12px;
        position: relative;
    }
    .payment-card.active .payment-circle {
        border-color: #FD914D;
    }
    .payment-card.active .payment-circle::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 10px;
        height: 10px;
        background-color: #FD914D;
        border-radius: 50%;
    }
    .payment-card span {
        font-weight: 600;
        font-size: 14px;
        color: #555;
    }
    .payment-card.active span {
        color: #333;
    }
    
    @media screen and (max-width: 800px) {
        .mt-mobile-fix {
            margin-top: 20px !important;
        }
    }
</style>