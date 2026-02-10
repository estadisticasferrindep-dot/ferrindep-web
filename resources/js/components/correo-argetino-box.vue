```html
    <div>
        <div v-if="loading"><i class="fa fa-spinner fa-spin"></i></div>
        <div class="correo-argetino-box__form" v-else>
            
            <!-- GOOGLE MAPS INPUT INJECTION -->
            <div class="form-group mt-2">
                <input 
                    id="cart-gps-input-vue" 
                    ref="autocompleteInput"
                    type="text" 
                    class="form-control form-control-sm" 
                    placeholder="Cambiar ubicación (ej: La Lonja)"
                    style="font-size: 13px; max-width: 300px;"
                >
            </div>

            <!-- HIDDEN SELECT FOR LEGACY COMPTAX -->
            <select v-model="destino_id" style="display:none;">
                 <option :value="null">Seleccione</option>
                 <option v-for="destino in destinos" :value="destino.nombre" :key="destino.id">{{ destino.nombre }}</option>
            </select>

            <div class="input-group mb-2" v-if="destino_id">
                <span class="input-group-text">Costo Estimado:</span>
                <div class="form-control">
                    <span v-if="getCaculatedShipping === 0" style="color: green; font-weight:bold;">¡Envío Gratis!</span>
                    <span v-else>$ {{ getCaculatedShipping | toCurrency }}</span>
                </div>
            </div>

             <div v-if="ciudadDetectada" style="font-size:12px; color:#28a745; margin-top:5px;">
                <i class="fas fa-check-circle"></i> Ubicación: <b>{{ ciudadDetectada }}</b>
            </div>
             <div v-if="errorMessage" style="font-size:12px; color:#dc3545; margin-top:5px;">
                <i class="fas fa-exclamation-circle"></i> {{ errorMessage }}
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['ubicacionPreasignada'],
    data() {
        return {
            loading: false,
            destinos: [],
            zonas: [],
            pesozonas: [],
            cart: [],
            destino_id: null,
            autoSelected: false,
            ciudadDetectada: '',
            errorMessage: ''
        }
    },
    created() {
        setInterval(() => {
            this.getCart()
        }, 500)
        this.getData();
    },
    mounted() {
        this.initAutocomplete();
        
        // Check if we have pre-assigned location and cost
        if (this.ubicacionPreasignada && this.ubicacionPreasignada.shipping_cost) {
            console.log("Pre-assigned cost found:", this.ubicacionPreasignada.shipping_cost);
            this.$emit('shipping-cost', this.ubicacionPreasignada.shipping_cost);
            // Also helpful to set the input value visual
            if (this.ubicacionPreasignada.cityName) {
                 // We prefer keeping the placeholder 'Cambiar...' but if user wants to see current?
                 // User said: "Escribes 'San Miguel'..." implies it starts empty or placeholder.
                 // But if we clearly show "Envío a Haedo", input can remain "Cambiar...".
            }
        }
    },
    methods: {
        initAutocomplete() {
            if (typeof google === 'undefined' || !google.maps || !google.maps.places) {
                console.warn("Google Maps not loaded yet.");
                setTimeout(this.initAutocomplete, 1000); // Retry
                return;
            }

            const input = this.$refs.gpsInput;
            const options = {
                types: ['geocode'],
                componentRestrictions: { country: 'ar' },
                fields: ['address_components', 'geometry']
            };

            const autocomplete = new google.maps.places.Autocomplete(input, options);

            autocomplete.addListener('place_changed', () => {
                const place = autocomplete.getPlace();
                if (!place.geometry) return;

                let city = '';
                let partido = '';
                let region = '';

                for (const component of place.address_components) {
                    const types = component.types;
                    if (types.includes('locality')) city = component.long_name;
                    if (!city && types.includes('sublocality')) city = component.long_name;
                    if (types.includes('administrative_area_level_2')) partido = component.long_name;
                    if (types.includes('administrative_area_level_1')) region = component.long_name;
                }

                // Fallback
                if (!city && place.address_components.length > 0) {
                    city = place.address_components[0].long_name;
                }

                this.ciudadDetectada = `${city} (${partido})`;
                this.resolveLocation(city, partido, region);
            });
        },
        resolveLocation(city, partido, region) {
            this.loading = true;
            this.errorMessage = '';
            
            // Get CSRF Token
            let token = document.head.querySelector('meta[name="csrf-token"]');
            
            fetch('/web/gps', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token ? token.content : '',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ manual_city: city, manual_partido: partido, manual_region: region })
            })
            .then(r => r.json())
            .then(data => {
                this.loading = false;
                if (data.mapped_id) {
                     // Find the Destino Object by ID to get its NAME (which is what v-model uses)
                     const found = this.destinos.find(d => d.id == data.mapped_id);
                     if(found) {
                         this.destino_id = found.nombre; // Trigger Computed
                     } else {
                         this.destino_id = null;
                         this.errorMessage = "No pudimos calcular el envío para esta zona. Consultanos.";
                     }
                } else {
                    this.destino_id = null;
                    this.errorMessage = "Zona desconocida. Consultar costo.";
                }
            })
            .catch(e => {
                this.loading = false;
                console.error(e);
                this.errorMessage = "Error de conexión.";
            });
        },
        getData() {
            this.loading = true;
            axios.get(this.$root.publicPath + '/api/carrito')
                .then(response => {
                    this.destinos = response.data.destinos;
                    this.zonas = response.data.zonas;
                    this.pesozonas = response.data.pesozonas;

                    this.loading = false;
                    
                    // Lógica de Autoselección (Stage 4)
                    if (this.ubicacionPreasignada && this.ubicacionPreasignada.nombre) {
                        const match = this.destinos.find(d => d.nombre === this.ubicacionPreasignada.nombre);
                        if (match) {
                            this.destino_id = match.nombre;
                            this.autoSelected = true;
                            this.ciudadDetectada = this.ubicacionPreasignada.cityName || 'tu ubicación';
                        }
                    }
                })
                .catch(error => {
                    console.log(error);
                    this.loading = false;
                });
        },
        getCart() {
            this.cart = JSON.parse(localStorage.getItem('cartQunuy'));
        }
    },
    computed: {
        getCaculatedShipping() {
            let destino = this.destinos.find(destino => destino.nombre == this.destino_id);
            if ( !destino ) {
                return 0;
            }
            // aca son los paquentes que se van a enviar
            // es un array, cada uno no puede exceder el peso maximo que son 25 por cada uno
            let packages = [];
            // aca itero los elementos del carrito
            if ( this.cart.length > 0 ) {
                this.cart.forEach(item => {
                    // defino la variable packageFound que por defecto va en false
                    // esto significa que no ha encontrado ningun paquete que cumpla con el peso maximo
                    let packageFound = false
                    // calculo el peso, que es una multiplicacion de la cantidad por el peso del producto
                    let weight = item.cantidad * item.peso;
                    // itero los paquetes que ya se han creado
                    // esto es para saber si el paquete entra en uno de los paquetes creados
                    packages.forEach(p => {
                        // aca va a validar que el peso del paquete no exceda el peso maximo
                        // y que si el paquete tiene envio gratis al igual que el item a agregar
                        if ( (p.total_size + weight) <= 25 && (!!item.free) == p.free ) {
                            // de encontrar un lugar que cumpla con el peso maximo
                            // la variable packageFound va a cambiar a true
                            packageFound = true;
                            // y sumo el peso al paquete que encontre
                            p.total_size += weight;
                        }
                    });
                    // chequeo si no se encontro ningun paquete que cumpla con el peso maximo
                    // de ser asi esta es la logica para crearlo
                    if ( !packageFound ) {
                        // chequeo si el producto se pasa de los 25kg
                        if ( weight > 25 ) {
                            // si el paquete de pasa de 25kg,
                            // entonces divido en grupos de 25kg
                            // por ejemplo si el producto pesa 60kg,
                            // packs tendra 2 paquetes, uno de 25kg y otro de 25kg
                            // y remainder tendra un paquete de 10kg
                            let packs = Math.floor(weight / 25);
                            let remainder = weight % 25;
                            for (let i = 0; i < packs; i++) {
                                packages.push({
                                    total_size: 25,
                                    cost: 0,
                                    free: !!item.free
                                });
                            }
                            if ( remainder > 0 ) {
                                packages.push({
                                    total_size: remainder,
                                    cost: 0,
                                    free: !!item.free
                                });
                            }

                        } else {
                            // de no pasar el peso maximo, creo un paquete con el peso del producto
                            packages.push({
                                total_size: item.cantidad * item.peso,
                                cost: 0,
                                free: !!item.free
                            })
                        }
                    }
                });
            }

            packages.forEach(p => {
                this.pesozonas.forEach(z => {
                    if ( p.total_size <= z.peso && z.zona_id == destino.zona_id && p.free == false ) {
                        p.cost = parseFloat(z.costo);
                    }
                });
            });
            let total = packages.reduce((a, b) => a + b.cost, 0);
            this.$emit('shipping-cost', total);
            this.$emit('destino_id',  this.destino_id);

            return total;
        }
    }
}
</script>

<style lang="scss" scoped>
    .correo-argetino-box {
        &__form {
            padding: 7.5px;
            border: 1px solid rgba(143, 134, 110, 0.3);
            margin: 7.5px 0;
        }
    }
    .input-group-text {
        background-color: #fff;
    }
</style>