<template>

<div>
    <div v-if="presentaciones2.length >= 1">
        <div class="container-fluid" style="padding:0">
            
            <div class="col-12" >
                <div v-if="presentaciones2.length >= 1 && desde > 0">
                    <p class="precio" style="text-align: left; font: normal normal bold 17px/25px Open Sans;
                    letter-spacing: 0px;
                    color: #FD914D;
                    margin-bottom:0">

                            <span style="font: normal normal normal 15px/20px Open Sans;color: #333333;"></span>
                            <!-- <span v-if="parseFloat(oferta)" class="precio-oferta">
                                ${{desdeOferta}} x mt
                            </span> -->
                            <span v-if="esPorMetro">
                                desde $ {{desde}} por metro
                            </span>
                            <span v-else>
                                $ {{desde}} - $ {{hasta}} <span v-if="conNombre  != 1"> x mt </span>
                            </span>
                    </p>
                </div>
                <div v-else>
                    <p class="precio" style="text-align: left; font: normal normal bold 25px/34px Open Sans;
                    letter-spacing: 0px;
                    color: #FD914D;
                    margin-bottom:0">
                            <span style="font: normal normal normal 15px/20px Open Sans;color: #333333;">Consultar precio</span>

                    </p>
                </div>
                    <div style="font: normal normal normal 12px/16px Open Sans; color: #FD914D; margin-top: 8px;"> Click para ver mas detalles</div>
                     <!-- Dynamic Shipping Label -->
                    <div v-if="dynamicShippingLabel" v-html="dynamicShippingLabel" style="margin-top:4px; line-height:1.2;"></div>

            </div>
            <div class="col-12">
                <div class="w-100 d-flex justify-content-between" style="font: normal normal normal 11px/19px Open Sans;
                letter-spacing: 0px;
                color: #939292;
                margin-top: 3px;">
                    <div v-if="vendidos > 0 ">{{vendidos}} vendidos</div>

                    <div>
                        <h5  style="font: normal normal normal 11px/19px Open Sans; margin-bottom:0;" v-if="descEfectivo > 0 ">-{{descEfectivo}}% off efectivo</h5>
                        <h5  style="font: normal normal normal 11px/19px Open Sans; margin-bottom:0;" v-if="descTransferencia > 0 ">-{{descTransferencia}}% off transferencia</h5>
                    </div>
                </div>
            </div>
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
            presentaciones: {},
            oferta: {type: Boolean},
            precioAnterior: {type: Number},
            vendidos: {type: Number},
            descEfectivo: {type: Number},
            descTransferencia: {type: Number},
            descMp: {type: Number},
            conNombre: {type: Number},
            itemId: {type: Number, default: 0},
            // RAW DATA FOR CLIENT-SIDE RESOLUTION
            zonas: { type: Array, default: () => [] },
            destinos: { type: Array, default: () => [] },
            destinoZonas: { type: Array, default: () => [] },
            locationName: { type: String, default: '' },
            flexZonePrice: { type: Number, default: 0 },
        },
        data() { 
            return {
                presentaciones2: {},
                presentacionElegido: null,
                desde:0,
                desdeOferta: 0,
                hasta:0,
                esPorMetro: false
            };
        },
        created() { 
            try {
                this.presentaciones2 = this.presentaciones ? JSON.parse(this.presentaciones) : [];
            } catch (e) {
                this.presentaciones2 = [];
            }
            console.log(this.presentaciones2)
            if(this.presentaciones2.length > 0) {
                this.desdeCalcular()
            }
        },
        computed: {
            dynamicShippingLabel() {
                // 1. Validate Location
                if (!this.locationName) return null;

                let cityName = `<b>${this.locationName}</b>`;

                // Helper: Calculate cost with weight-based bultos (same as product detail page)
                const calcWithBultos = (baseCost) => {
                    // Use the first presentation's weight as representative
                    if (this.presentaciones2 && this.presentaciones2.length > 0) {
                        let peso = parseFloat(this.presentaciones2[0].peso || 0);
                        if (peso > 0) {
                            let bultos = Math.max(1, Math.ceil(peso / 30));
                            return baseCost * bultos;
                        }
                    }
                    return baseCost; // fallback: 1 bulto
                };

                // 2. PRIORITY: If server resolved a Flex zone price, use it directly
                //    This matches how the product detail page calculates shipping
                if (this.flexZonePrice > 0) {
                    // Check free shipping first
                    let hasFreeOption = false;
                    if (this.presentaciones2 && this.presentaciones2.length > 0) {
                        for (let p of this.presentaciones2) {
                            if (p.envio_gratis == 1 || p.envio_gratis_flex == 1) hasFreeOption = true;
                        }
                    }
                    if (hasFreeOption) {
                        return `<span style="color:#28a745; font-weight:bold; font-size:11px;">
                                    <i class="fas fa-truck"></i> Envío gratis a ${cityName}
                                </span>`;
                    }

                    let finalCost = calcWithBultos(this.flexZonePrice);
                    let formattedCost = Math.round(finalCost).toLocaleString('es-AR');
                    return `<span style="color:#FD914D; font-size:11px;">
                                <i class="fas fa-truck"></i> Envío a ${cityName} desde <b>$${formattedCost}</b>
                            </span>`;
                }

                // 3. FALLBACK: Client-Side Legacy Resolution
                let city = this.locationName.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "").trim();
                
                let destino = this.destinos.find(d => 
                    d.nombre.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "").trim() === city
                );

                if (!destino) {
                    return `<span style="color:#FD914D; font-size:11px;">
                                <i class="fas fa-truck"></i> Envío a ${cityName}: <b>Consultar</b>
                            </span>`;
                }

                let rel = this.destinoZonas.find(dz => dz.destino_id == destino.id);
                if (!rel) {
                    return `<span style="color:#FD914D; font-size:11px;">
                                <i class="fas fa-truck"></i> Envío a ${cityName}: <b>Consultar</b>
                            </span>`;
                }

                let zoneId = rel.zona_id;
                let zone = this.zonas.find(z => z.id == zoneId);
                
                if (!zone) {
                    return `<span style="color:#FD914D; font-size:11px;">
                                <i class="fas fa-truck"></i> Envío a ${cityName}: <b>Consultar</b>
                            </span>`;
                }

                let zonePrice = parseFloat(zone.precio || zone.costo || 0);

                // Check Matching Free Shipping flags
                let hasFreeOption = false;
                if (this.presentaciones2 && this.presentaciones2.length > 0) {
                     for(let p of this.presentaciones2) {
                        if (zoneId == 1 && p.envio_gratis_zona_1 == 1) hasFreeOption = true;
                        if (zoneId == 2 && p.envio_gratis_zona_2 == 1) hasFreeOption = true;
                        if (zoneId == 3 && p.envio_gratis_zona_3 == 1) hasFreeOption = true;
                        if (zoneId == 4 && p.envio_gratis_zona_4 == 1) hasFreeOption = true;
                        if (p.envio_gratis == 1) hasFreeOption = true;
                     }
                }

                if (hasFreeOption) {
                     return `<span style="color:#28a745; font-weight:bold; font-size:11px;">
                                <i class="fas fa-truck"></i> Envío gratis a ${cityName}
                            </span>`;
                }

                // Apply bultos calculation to Legacy price too
                let finalCost = calcWithBultos(zonePrice);
                let formattedCost = Math.round(finalCost).toLocaleString('es-AR');
                return `<span style="color:#FD914D; font-size:11px;">
                            <i class="fas fa-truck"></i> Envío a ${cityName} desde <b>$${formattedCost}</b>
                        </span>`;
            }
        },
        methods: {
            formatPrice(value) {
                let val = Math.round(value).toString();
                return val.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            },
            desdeCalcular(){
                if(!this.presentaciones2 || this.presentaciones2.length === 0) return;

                // Lógica especial para Producto 1 (10x10mm 30cm) o si se expande a otros
                if(this.itemId == 1) {
                     this.esPorMetro = true;
                     // Buscar presentación con mas metros (rollo mas largo)
                     let maxMetros = 0;
                     let precioRolloMasLargo = 0;
                     
                     this.presentaciones2.forEach(p => {
                         let m = parseFloat(p.metros);
                         if(m > maxMetros) {
                             maxMetros = m;
                             precioRolloMasLargo = p.precio;
                         }
                     });

                     if(maxMetros > 0) {
                         // Precio por metro del rollo mas largo
                         this.desde = Math.round(precioRolloMasLargo / maxMetros);
                         this.hasta = 0; // Ocultar hasta
                     }
                } else if(this.conNombre == 1){
                    let preciosXMetro  = this.presentaciones2.map(function(p) {
                        return p.precio;
                    });
                    this.desde = Math.min.apply(null, preciosXMetro)
                    this.hasta = Math.max.apply(null, preciosXMetro)

                    let preciosXMetroOferta  = this.presentaciones2.map(function(p) {
                        return p.precio_anterior;
                    });
                    this.desdeOferta = Math.min.apply(null, preciosXMetroOferta)
                }
                else{
                    let preciosXMetro  = this.presentaciones2.map(function(p) {
                        return Math.round((p.precio)/p.metros);
                    });
                    this.desde = Math.min.apply(null, preciosXMetro)
                    this.hasta = Math.max.apply(null, preciosXMetro)

                    let preciosXMetroOferta  = this.presentaciones2.map(function(p) {
                        return Math.round((p.precio_anterior)/p.metros);
                    });
                    this.desdeOferta = Math.min.apply(null, preciosXMetroOferta)
                }
                
            },
            selectpresentacion(presentacion){
                this.presentacionElegido = presentacion
                this.price= presentacion.precio
            },
            formatPrice(value) {
                let val = (value/1).toFixed(2).replace('.', ',')
                return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
            }
        },

        
    };
</script>

<style scoped>

    .presentacion {
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


    .presentacion:hover, .presentacion.select{
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