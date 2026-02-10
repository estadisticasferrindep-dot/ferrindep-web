
<template>
    <a class="carrito nav-link sin-borde" :class="{'d-flex flex-column align-items-center justify-content-start p-0': icon}" >
        <span v-show="itemsCount!=0" style="position: absolute;
    color: white;
    font: normal normal bold 10px/12px Open Sans;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 50%;
    top: 30px;
    left: 24px;
    width: 15px;
    height: 15px;
    background-color: #FD914D;
    padding: 1px;
    cursor: pointer;"> {{itemsCount}} </span>

        <i v-if="icon" :class="icon" style="font-size: 22px; color: white; line-height: 1;"></i>
        <img v-else :src="img" style="width: 40px;     margin-right: 9px;" >

        <div v-if="icon" class="d-md-none" style="font-size: 9px !important; line-height: 1.1 !important; margin-top: 2px; color: white !important; text-align: center; display: block !important; visibility: visible !important;">
            Orden<br>de Compra
        </div>

        <div class="d-none d-md-flex">VER ORDEN DE COMPRA</div>
    </a>
</template>
<script>
//     import anime from 'animejs';

    export default {
        name: 'CartWidget',
       
        props:{
            img: {},
            icon: {}
        },
        data() {
        return {
            itemsCount: 0,
            cart: []
        };
        },
        mounted() {
            this.$root.$on('count', data => {
                this.count();
            });
        },
        created() {
            this.count();
        },
        methods: {
            count(){
                this.cart = window.getCartSafe();
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

.carrito span {
    position: absolute;
    color: white;
    font: normal normal bold 10px/12px Open Sans;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 50%;
    top: 0; /* Changed from bottom */
    right: 0; /* Adjusted */
    width: 15px;
    height: 15px;
    background-color: #FD914D; /* Match styling */
    padding: 1px;
    cursor: pointer;
    z-index: 5;
}


.carrito i{
    /* width: 14px !important; REMOVED to fix mobile overlap */
    /* height: 13px !important; REMOVED to fix mobile overlap */
    color: #EEEEEE;
    font-size: 16px;
    cursor: pointer;
}

    @media screen and (max-width: 800px) {
        /* FIX: Remove aggressive img styling that breaks the icon size */
        /* img{
            height: 39px !important;
            margin-top:0 !important;
        } */
    } 

        
</style>