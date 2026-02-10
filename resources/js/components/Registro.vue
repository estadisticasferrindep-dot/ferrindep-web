<template>
    <div>
        <h3>PASO 1 - REGÍSTRESE</h3>
        <input class="col-12 box" name="email" v-model="email" placeholder="Email *" style="margin-bottom:14px;">
        <div class="row">
            <!-- <div class="col-12 col-md-6">
                <input class="box" name="nombre" v-model="nombre" placeholder="Nombre *" style="margin-bottom:14px;">
                <input class="box" name="empresa" v-model="empresa" placeholder="Empresa">
            </div>
    
            <div class="col-12 col-md-6">
                <input class="box" name="telefono" v-model="telefono"  placeholder="Número de teléfono *" style="margin-bottom:14px;">
                <input class="box" name="cuit" v-model="cuit"  placeholder="CUIT">
            </div>
            <div class="col-12">
                <input class="col-12 box" name="direccion" v-model="direccion"  placeholder="Dirección" style="margin-bottom:14px;">
            </div> -->
            <div class="col-12 col-md-6">
                <input class="box" type="password" name="password" v-model="password"  placeholder="Contraseña *" style="margin-bottom:14px;">
            </div>
            <div class="col-12 col-md-6">
                <input class="box" type="password" name="password_confirmation" v-model="password_confirmation"  placeholder="Repetir contraseña *">
            </div>
        </div>
        <div class="row">
            <div class="col-3 col-md-7"></div>

            <div class="col-9 col-md-5" style="display: flex;justify-content: flex-end;">
                <button @click="enviar" type="submite" class="contacto-btn col-5" >
                REGISTRARSE 
                </button>
            </div>
        </div>  
    </div>             
</template>
<script>
    import Swal from 'sweetalert2'; 
    export default {
        props:{
            target: ''
        },
        data() {
            return {
                password_confirmation: '',
                password: '',
                direccion: '',
                cuit: '',
                telefono: '',
                empresa: '',
                nombre: '',
                email: ''
            }
        },
        watch:{
        
        },
        created() {
            
        },
        methods: {
            enviar(){
                var form = new FormData();

                form.append('password_confirmation', this.password_confirmation);
                form.append('password', this.password);
                form.append('direccion', this.direccion);
                form.append('cuit', this.cuit);
                form.append('telefono', this.telefono);
                form.append('empresa', this.empresa);
                form.append('nombre', this.nombre);
                form.append('email', this.email);

                axios.post(this.target, form).then((response) => {
                    Swal.fire(
                    'Buen trabajo! ',
                    response.data.message +'. No olvide entrar a su cuenta recién creada antes de comprar!' ,
                    'success'
                    )  
                }).catch(error=> {
                    error.response.data
                    console.log(error.response.data);
                    Swal.fire(
                    'Se encontraron los siguientes errores',
                    error.response.data.errors.email+ ' ' + error.response.data.errors.password,
                    'error'
                    )  
                })

            }
        }
    }
</script>

<style lang="scss" scoped>


</style>