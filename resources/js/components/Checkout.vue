<template>
    <div class="section-envio">
        
        <div class="fila" style="margin-bottom: 20px;">
             <label style="font-size: 20px;">Subtotal</label>
             <span class="precio" style="font-size: 22px;">${{ subtotal | toCurrency }}</span> 
        </div>
        
        <hr>

        <div class="opcion-envio mt-3 mb-3">
             <div class="d-flex align-items-center mb-2">
                <input type="radio" name="envio" id="envio_domicilio" 
                       value="caba" 
                       v-model="envio"
                       :disabled="anulaEnvio">
                <label for="envio_domicilio" class="ml-2 mb-0 font-weight-bold" style="font-size:16px; display:flex; flex-direction:column;">
                    <span :style="{ color: anulaEnvio ? '#ccc' : 'inherit' }">Envío a domicilio</span>
                    <span v-if="anulaEnvio" style="font-size:11px; color:#d63031; font-weight:normal;">NO DISPONIBLE</span>
                </label>
             </div>

             <div v-if="envio === 'caba'" class="ml-4 pl-1">
                 
                 <div v-if="!editandoUbicacion && localidadDetectada">
                     <div class="d-flex justify-content-between align-items-center">
                         <div>
                             <p class="mb-0" style="font-size: 16px; color: #FD914D; font-weight: bold; display: flex; align-items: center;">
                                 <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt-fill" viewBox="0 0 16 16" style="margin-right: 5px;">
                                     <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                                 </svg>
                                 {{ localidadDetectada }}
                             </p>
                             <small v-if="ubicacionPreasignada && ubicacionPreasignada.partidoName && !localidadManual" class="text-muted">
                                 ({{ ubicacionPreasignada.partidoName }})
                             </small>
                             <a href="#" @click.prevent="activarEdicion" style="font-size:12px; display:block; color:#999; margin-top:2px;">(Cambiar)</a>
                         </div>
                         
                         <div class="text-right">
                             <span v-if="envio_total > 0" class="precio" style="font-size: 16px;">${{ envio_total | toCurrency }}</span>
                             <span v-else-if="envio_total === 0 && destinoId > 0 && !loadingCost" class="precio" style="font-size: 16px; color: #28a745; font-weight: bold;">¡Envío Gratis!</span>
                             <span v-else-if="loadingCost" class="precio" style="font-size: 16px;">Calculando...</span>
                             <span v-else class="precio" style="font-size: 16px;">Consultar</span>
                         </div>
                     </div>
                 </div>

                 <div v-else>
                     <input id="google-autocomplete-cart" type="text" class="form-control" placeholder="Ingrese su localidad..." autocomplete="off">
                 </div>
             </div>
        </div>

        <div class="opcion-envio mb-3">
             <div class="d-flex align-items-center">
                <input type="radio" name="envio" id="envio_fabrica" value="fabrica" v-model="envio">
                <label for="envio_fabrica" class="ml-2 mb-0" style="font-size:16px;">Retiro por depósito</label>
             </div>
             <div v-if="envio === 'fabrica'" class="ml-4 mt-1 text-muted" style="font-size:13px;">
                <p class="mb-0" v-if="parrafoEnvioFabrica" v-html="parrafoEnvioFabrica"></p>
                <p class="mb-0" v-else>Puede retirar su pedido por nuestra fábrica sin costo.</p>
                
                <!-- NEW WARNING -->
                <p v-if="anulaEnvio" class="mt-2 text-danger font-weight-bold" style="font-size:12px;">
                    <i class="fas fa-exclamation-circle"></i> Su carrito contiene productos que no pueden ser enviados.
                </p>
            </div>
        </div>

        <hr>

        <div class="fila mt-3 mb-3">
             <label style="font-size: 22px; font-weight: bold;">Total</label>
             <span class="precio" style="font-size: 24px; font-weight: bold; color: #FD914D;">${{ total | toCurrency }}</span> 
        </div>

        <div class="mt-4">
            <button @click="finalizar" class="btn-solid mb-2">CONTINUAR COMPRA</button>
            <button @click="productos" class="btn-solid">AGREGAR MÁS PRODUCTOS</button>
        </div>

    </div>
</template>

<script>
    import Swal from 'sweetalert2';

    export default {
        props:{
            rutaProductos:'',
            envios:{}, destinos:{}, zonas:{}, destinozonas:{}, pesozonas:{},
            login:{}, target: '',
            parrafoEnvioFabrica: {type:String},
            costoEnvioFabrica: {type: Number},
            step: {}, subtotal:{}, total:{}, envio2:{}, 
            ubicacionPreasignada: { type: Object, default: () => ({}) },
            cart: { type: Array, default: () => [] } // Received from Parent
        },
        data() {
            return {
                envio:'caba',
                editandoUbicacion: false,
                shippingCost: 0, 
                unitShippingPrice: 0, 
                flexBaseTariff: 0,
                envio_total: 0,
                destinoId: 0,
                loadingCost: false,
                
                localidadManual: null, 

                // Hydrated Data
                destinos2: [], zonas2: [], destinozonas2: [],
                // cart: [], // REMOVED: Using Prop
                cantidadItems: 0,
                pesoTotal: 0,
                bultos: 1,
                
                anulaEnvio: false,
                autocompleteInstance: null,
                isDataReady: false 
            }
        },
        computed: {
            localidadDetectada() {
                // 1. Manual override
                if (this.localidadManual) return this.localidadManual;

                // 2. Prop override
                if (this.ubicacionPreasignada) {
                    // Robust check: try common keys
                    const up = this.ubicacionPreasignada;
                    const val = up.cityName || up.city_name || up.city || up.localidad || up.nombre || up.name;
                    if (val) return val;
                }
                return null;
            }
        },
        watch:{
            shippingCost() { this.recalcularTodo(); },
            destinos2: {
                handler(val) {
                    if (val && val.length > 0) {
                        this.isDataReady = true;
                        if (this.localidadDetectada) {
                            this.triggerAutoStart();
                        }
                    }
                },
                deep: true
            },
            cart: {
                handler() { this.processCartData(); },
                deep: true
            },
            envio(val) {
                if (val === 'caba') {
                     if (this.localidadDetectada) this.editandoUbicacion = false;
                     else this.activarEdicion();
                }
                this.recalcularTodo();
            },
            subtotal() { this.recalcularTodo(); },
            total() {  },
            ubicacionPreasignada: {
                handler(val) {
                    // Deep watch for dynamic updates (AJAX)
                    // Guard: only trigger if we haven't resolved location yet
                    if (this.localidadDetectada && !this._locationResolved) {
                         this._locationResolved = true;
                         this.localidadManual = null;
                         this.editandoUbicacion = false;
                         this.$nextTick(() => {
                             this.triggerAutoStart();
                         });
                    }
                },
                deep: true
            }
        },

        created() {
            this.processCartData(); // Init calculation from prop
            
            const hydrate = (prop) => {
                if (!prop) return [];
                if (Array.isArray(prop) || typeof prop === 'object') return prop;
                try { return JSON.parse(prop); } catch (e) { return []; }
            };

            this.destinos2 = hydrate(this.destinos);
            this.zonas2 = hydrate(this.zonas);
            this.destinozonas2 = hydrate(this.destinozonas);
            
            if (this.destinos2.length > 0) this.isDataReady = true;

            this.seAnulaEnvio();
            this.$nextTick(() => { this.recalcularTodo(); });
        },
        
        mounted() {
            this.$root.$on('seAnulaEnvio', () => this.seAnulaEnvio());

            // AUTO-START LOGIC
            if (this.localidadDetectada) {
                this.envio = 'caba';
                this.editandoUbicacion = false; 
                
                if (this.isDataReady) {
                    this.triggerAutoStart();
                }
            } else {
                // No initial location detected
                this.editandoUbicacion = true;
                if(this.envio === 'caba') this.initAutocomplete();
            }
        },
        
        methods: {
            triggerAutoStart() {
                const loc = this.localidadDetectada;
                if (loc) {
                    const up = this.ubicacionPreasignada || {};
                    const partido = up.partidoName || up.partido || up.administrative_area_level_2 || '';
                    const region = up.regionName || up.region || up.administrative_area_level_1 || '';
                    
                    setTimeout(() => {
                        this.resolveLocation(loc, partido, region);
                    }, 200);
                }
            },

            processCartData() {
                // Use THIS.CART (Prop) directly
                this.pesoTotal = 0;
                this.cantidadItems = 0;

                this.cart.forEach(item => {
                    let qty = parseInt(item.cantidad) || 1;
                    this.cantidadItems += qty;
                    let w = parseFloat(item.weight || item.peso || 0);
                    this.pesoTotal += (w * qty);
                });

                if (this.pesoTotal === 0 && this.cantidadItems > 0) {
                    this.bultos = 1; 
                } else {
                    this.bultos = Math.ceil(this.pesoTotal / 30);
                }
                if(this.bultos < 1) this.bultos = 1;

                this.seAnulaEnvio(); // Re-check invalid products

                // Recalculate shipping based on updated bultos
                // Note: shippingCost watcher calls recalcularTodo() automatically
                if (this.unitShippingPrice > 0) {
                     // Legacy / Client Side Logic
                    let effectiveBultos = this.calculateEffectiveBultos(this.lastZoneId); 
                    this.shippingCost = this.unitShippingPrice * effectiveBultos;
                } else if (this.flexBaseTariff > 0) {
                    // Flex mode: recalculate instantly using stored base tariff × client bultos
                    this.shippingCost = this.flexBaseTariff * this.bultos;
                }
                // Note: if no price resolved yet, do nothing — initial triggerAutoStart handles it
            },

            activarEdicion() {
                this.editandoUbicacion = true;
                this.localidadManual = null; 
                this._locationResolved = false; // Allow re-resolve on next location change
                this.autocompleteInstance = null; 
                this.$nextTick(() => this.initAutocomplete());
            },

            initAutocomplete() {
                if (this.autocompleteInstance) return; 

                this.$nextTick(() => {
                    setTimeout(() => {
                        const input = document.getElementById('google-autocomplete-cart');
                        if (!input) return; 
                        if (!window.google || !window.google.maps) return;
                        
                        try {
                            this.autocompleteInstance = new google.maps.places.Autocomplete(input, {
                                types: ['geocode'],
                                componentRestrictions: { country: 'ar' }
                            });

                            this.autocompleteInstance.addListener('place_changed', () => {
                                const place = this.autocompleteInstance.getPlace();
                                let city='', partido='', region='';
                                if (place.address_components) {
                                    for (const c of place.address_components) {
                                        if (c.types.includes('locality')) city = c.long_name;
                                        if (!city && c.types.includes('sublocality')) city = c.long_name;
                                        if (c.types.includes('administrative_area_level_2')) partido = c.long_name;
                                        if (c.types.includes('administrative_area_level_1')) region = c.long_name;
                                    }
                                }
                                this.resolveLocation(city, partido, region);
                            });
                        } catch(e) { console.error("Map Init Error:", e); }
                    }, 500);
                });
            },

            calculateEffectiveBultos(zonaId) {
                if (!zonaId) return this.bultos;
                let pesoPayable = 0;
                let countPayable = 0;

                this.cart.forEach(item => {
                    let isFree = false;
                    if (zonaId == 1 && item.envio_gratis_zona_1 == 1) isFree = true;
                    if (zonaId == 2 && item.envio_gratis_zona_2 == 1) isFree = true;
                    if (zonaId == 3 && item.envio_gratis_zona_3 == 1) isFree = true;
                    if (zonaId == 4 && item.envio_gratis_zona_4 == 1) isFree = true;

                    if (!isFree) {
                        let w = parseFloat(item.weight || item.peso || 0);
                        let q = parseInt(item.cantidad || 1);
                        pesoPayable += (w * q);
                        countPayable += q;
                    }
                });

                if (countPayable === 0) return 0; 
                let b = Math.ceil(pesoPayable / 30);
                return b < 1 ? 1 : b;
            },

            resolveLocation(city, partido, region) {
                // RESET
                this.shippingCost = 0;
                this.unitShippingPrice = 0; 
                this.destinoId = 0;
                this.lastZoneId = null;
                this.$emit('update:costoEnvio', 0);
                this.$emit('update:destinoId', 0);
                this.loadingCost = true;
                
                const normalize = (str) => str ? str.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "").trim() : "";

                let nCityCheck = normalize(city);
                if (nCityCheck === 'buenos aires' || nCityCheck === 'ciudad autonoma de buenos aires' || nCityCheck === 'capital federal') {
                    city = "CABA";
                }

                this.localidadManual = city;

                // ALWAYS go to server first (Flex system has correct tarifas)
                this.fetchPriceFromServer(city, partido, region);
            },

            fetchPriceFromServer(city, partido, region) {
                let token = document.head.querySelector('meta[name="csrf-token"]')?.content;
                this.loadingCost = true;

                // 1. Update Session GPS
                fetch('/web/gps', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                    body: JSON.stringify({ manual_city: city, manual_partido: partido, manual_region: region })
                })
                .then(r => r.json())
                .then(data => {
                    // 2. Calculate Shipping Cost via Backend
                    // We pass the mapped_id if available, but the controller also checks the session we just updated.
                    return fetch('/carrito/session/calculate-shipping', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                        body: JSON.stringify({ destino_id: data.mapped_id })
                    }).then(r2 => r2.json());
                })
                .then(data => {
                    this.loadingCost = false; 
                    
                    if (data.costo_envio !== undefined) {
                        // Store base tariff for client-side recalculation on qty changes
                        if (data.tarifa_base) {
                            this.flexBaseTariff = parseFloat(data.tarifa_base);
                        } else {
                            this.flexBaseTariff = 0;
                        }
                        
                        // Calculate bultos from client cart (inline, no side effects)
                        let clientPeso = 0;
                        this.cart.forEach(item => {
                            let q = parseInt(item.cantidad) || 1;
                            let w = parseFloat(item.weight || item.peso || 0);
                            clientPeso += (w * q);
                        });
                        let clientBultos = clientPeso > 0 ? Math.ceil(clientPeso / 30) : 1;
                        if (clientBultos < 1) clientBultos = 1;
                        
                        if (this.flexBaseTariff > 0) {
                            this.shippingCost = this.flexBaseTariff * clientBultos;
                        } else {
                            this.shippingCost = parseFloat(data.costo_envio);
                        }
                        
                        this.unitShippingPrice = 0; // Disable legacy local recalc
                        this.destinoId = data.zona || 0;
                        
                        this.$emit('update:costoEnvio', this.shippingCost);
                        this.$emit('update:destinoId', this.destinoId);
                        this.editandoUbicacion = false; 
                        this.recalcularTodo();
                    } else if (data.tipo === 'flex_free') {
                        // Free Flex
                        this.shippingCost = 0;
                        this.unitShippingPrice = 0;
                        this.$emit('update:costoEnvio', 0);
                        this.editandoUbicacion = false;
                        this.recalcularTodo();
                    } else {
                        // Error or No Coverage
                        console.warn("Shipping Error:", data.error);
                        this.editandoUbicacion = false; 
                        // reset to consult
                        this.shippingCost = 0;
                        this.destinoId = 0;
                        this.recalcularTodo();
                    }
                })
                .catch((e) => {
                    console.error("Error fetching price:", e);
                    this.loadingCost = false; 
                    this.editandoUbicacion = false; 
                    this.recalcularTodo();
                });
            },

            recalcularTodo() {
                if(this.envio == 'fabrica'){
                    this.envio_total = this.costoEnvioFabrica || 0;
                } else if(this.envio == 'caba'){
                    this.envio_total = this.shippingCost || 0;
                } else {
                    this.envio_total = 0;
                }
                
                let sub = parseFloat(this.subtotal) || 0;
                let env = parseFloat(this.envio_total) || 0;
                this.$emit('update:costoEnvio', env);
                this.$emit('update:total', sub + env);    
            },
            
            productos(){ window.location.href = this.rutaProductos; },
            
            seAnulaEnvio(){
                this.anulaEnvio = false;
                if(this.cart) {
                    this.cart.forEach(item => {
                        if (item.anulaEnvio == "1") {
                            this.anulaEnvio = true;
                            this.envio = 'fabrica';
                        }
                    });
                }
            },
            
            finalizar(){ 
                if (!this.cart || this.cart.length === 0) {
                    Swal.fire('Atención', 'Agregue productos a su orden de compra para continuar', 'warning');
                    return;
                }

                if(this.envio == "caba" && (!this.destinoId)){
                    Swal.fire('Atención', 'Por favor ingrese una localidad válida para cotizar el envío.', 'warning');
                } else {
                    this.$emit('update:step', 1);
                    this.$emit('update:envio2', this.envio);
                }
            }
        }
    }
</script>

<style lang="scss" scoped>
    .section-envio{ padding:0px 22px 39px 22px; }
    hr{ border: 0.5px solid rgba(143, 134, 110, 0.3); }
    .fila{ display: flex; align-items: center; justify-content: space-between; margin-top:13px; }
    .btn-solid {
        text-align: center; font: normal normal bold 14px/17px Open Sans;
        letter-spacing: 0.56px; color: #FFFFFF; background: #FD914D;
        border: 1px solid #FD914D; border-radius: 8px; padding: 11px 0;
        width: 100%; cursor: pointer; display: block; text-transform: uppercase; 
    }
    input[type="radio"] {
        -webkit-appearance: none; appearance: none; background-color: #fff;
        margin: 0; font: inherit; color: currentColor; width: 20px; height: 20px;
        border: 2px solid #FD914D; border-radius: 50%; display: grid;
        place-content: center; cursor: pointer; margin-right: 8px; flex-shrink: 0;
    }
    input[type="radio"]::before {
        content: ""; width: 10px; height: 10px; border-radius: 50%;
        transform: scale(0); transition: 120ms transform ease-in-out;
        box-shadow: inset 1em 1em #FD914D; background-color: #FD914D;
    }
    input[type="radio"]:checked::before { transform: scale(1); }
    input[type="radio"]:focus { outline: none; }
    @media screen and (max-width: 800px) {
        .section-envio { padding: 0px 15px 20px 15px !important; }
    }
</style>