<template>
    <div>
        <h3>PASO 2 - INICIE SESIÓN</h3>
        <input class="col-12 box" name="email" v-model="email" placeholder="Email *" style="margin-bottom:14px;">
        <input class="col-12 box" type="password" name="password" v-model="password" placeholder="Contraseña *">
        <div class="row">


            <div class="col-3 col-md-6"></div>
            <div class="col-9 col-md-6" style="display: flex;justify-content: flex-end;">
                <button @click="enviar" type="submite" class="contacto-btn col-6" >
                INICIAR SESIÓN 
                </button>
            </div>
        </div>
        
    </div>              
</template>
<script>
import Swal from 'sweetalert2'; 
    export default {
        props:{
            target: '',
            ruta: ''
        },
        data() {
            return {
                email: '',
                password: ''
            }
        },
        watch:{
           
        },
        created() {
        
        },
        methods: {
            enviar(){
                var form = new FormData();
                form.append('password', this.password);
                form.append('email', this.email);

                axios.post(this.target, form).then((response) => {
                    Swal.fire(
                    ' ',
                    response.data.message,
                    response.data.status
                    )
                    if(response.data.status == 'success'){
                    setTimeout(()=>{
                        window.location = this.ruta
                    },1000) 
                    } 
                    
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