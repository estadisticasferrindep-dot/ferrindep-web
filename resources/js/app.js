/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

// ✅ Helper global: NO depende de resources/js/cartHelper.js
window.getCartSafe = function () {
    try {
        const raw = localStorage.getItem("cartQunuy");
        if (!raw) return [];
        const parsed = JSON.parse(raw);
        return Array.isArray(parsed) ? parsed : [];
    } catch (e) {
        localStorage.removeItem("cartQunuy");
        return [];
    }
};

window.publicPath = () => {
    let meta = document.querySelector('meta[name="public-path"]');
    if (meta) {
        return meta.getAttribute('content').replace(/\/$/, '');
    }
    return '';
};

window.Vue = require('vue').default;

// Componentes
Vue.component('add-to-cart', require('./components/AddToCart.vue').default);
Vue.component('cart', require('./components/Cart.vue').default);
Vue.component('carrito', require('./components/Carrito.vue').default);
Vue.component('cart-list', require('./components/CartList.vue').default);
Vue.component('checkout', require('./components/Checkout.vue').default);
Vue.component('finalizar-compra', require('./components/FinalizarCompra.vue').default);

Vue.component('galeria-producto', require('./components/GaleriaProducto.vue').default);
Vue.component('diametros-categorias', require('./components/DiametrosCategorias.vue').default);
Vue.component('rangos-categorias', require('./components/RangosCategorias.vue').default);
Vue.component('desde-categorias', require('./components/DesdeCategorias.vue').default);
Vue.component('nombre-hover', require('./components/NombreHover.vue').default);

Vue.component('login', require('./components/Login.vue').default);
Vue.component('registro', require('./components/Registro.vue').default);

// Utils
window.toCurrency = (numero) => {
    let decimales = 2;

    let separadorDecimal = document.head.querySelector('meta[name="decimal-separator"]');
    separadorDecimal = separadorDecimal ? separadorDecimal.content : ',';

    let separadorMiles = document.head.querySelector('meta[name="thousands-separator"]');
    separadorMiles = separadorMiles ? separadorMiles.content : '.';

    let partes, array;

    if (!isFinite(numero) || isNaN(numero = parseFloat(numero))) {
        return "";
    }

    // Redondeo
    if (!isNaN(parseInt(decimales))) {
        if (decimales >= 0) {
            numero = numero.toFixed(decimales);
        } else {
            numero = (
                Math.round(numero / Math.pow(10, Math.abs(decimales))) * Math.pow(10, Math.abs(decimales))
            ).toFixed();
        }
    } else {
        numero = numero.toString();
    }

    // Formato
    partes = numero.split(".", 2);
    array = partes[0].split("");
    for (var i = array.length - 3; i > 0 && array[i - 1] !== "-"; i -= 3) {
        array.splice(i, 0, separadorMiles);
    }
    numero = array.join("");

    if (partes.length > 1) {
        numero += separadorDecimal + partes[1];
    }

    return numero;
};

Vue.filter('toCurrency', window.toCurrency);

// App Vue
const app = new Vue({
    el: '#app',
    data: {
        publicPath: window.publicPath(),
    },
    created() {
        console.log("ANTIGRAVITY_WAS_HERE");
    }
});
