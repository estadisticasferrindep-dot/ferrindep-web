<template>
  <div class="producto-gallery">
    <!-- Carrusel principal -->
    <div
      id="heroCarousel"
      class="carousel slide mb-2"
      data-bs-touch="true"
      data-bs-interval="false"
      ref="heroCarousel"
    >
      <div class="carousel-inner">
        <div
          v-for="(galeria, index) in galerias"
          :key="index"
          :class="['carousel-item', { active: index === currentIndex }]"
        >
          <!-- Imagen estática o video en el segundo slide -->
          <div
            v-if="!(video && index === 1)"
            class="hero-img-wrapper"
            :style="'background-image: url(' + galeria.imagen_url + ');'"
          >
            <span class="oferta" v-if="parseFloat(oferta)">OFERTA</span>
          </div>

          <!-- Video embebido en el segundo slide, si existe -->
          <iframe
            v-else
            class="video-prod"
            width="100%"
            style="height: 56vw; max-height: 500px;"
            :src="`https://www.youtube.com/embed/${video}?loop=1&autoplay=1&playlist=${video}`"
            title="Video del producto"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen
          ></iframe>
        </div>
      </div>

      <!-- Flechas de navegación -->
      <button
        class="carousel-control-prev"
        type="button"
        data-bs-target="#heroCarousel"
        data-bs-slide="prev"
        aria-label="Anterior"
      >
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      </button>
      <button
        class="carousel-control-next"
        type="button"
        data-bs-target="#heroCarousel"
        data-bs-slide="next"
        aria-label="Siguiente"
      >
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
      </button>
    </div>

    <!-- Miniaturas -->
    <div class="thumbs-wrapper d-flex gap-2 overflow-auto">
      <button
        v-for="(galeria, index) in galerias"
        :key="'thumb-' + index"
        class="thumb-btn"
        :class="{ active: index === currentIndex }"
        @click="changeSlide(index)"
      >
        <img
          :src="galeria.imagen_url"
          class="thumb-img"
          :alt="'Miniatura ' + index"
          loading="lazy"
        />
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'GaleriaProducto',
  props: {
    oferta: { type: Boolean },
    video: { type: [String, Number] }, // acepta string o número para el ID de YouTube
  },
  data() {
    return {
      galerias: [],
      currentIndex: 0,
      myCarousel: null,
    };
  },
  created() {
    // cargamos el array global de imágenes y movemos la elegida al inicio
    const allGal = window.galerias ? [...window.galerias] : [];
    if (allGal.length) {
      this.galerias = allGal;
      // asegurarnos de que el primer elemento sea la imagen principal
      this.currentIndex = 0;
    }
  },
  mounted() {
    // Inicializamos el carrusel Bootstrap cuando el componente esté montado
    this.myCarousel = new bootstrap.Carousel(this.$refs.heroCarousel, {
      interval: false,
      ride: false,
      touch: true,
      keyboard: true,
    });

    // Escuchamos el evento para actualizar currentIndex cuando el usuario navega
    this.$refs.heroCarousel.addEventListener('slid.bs.carousel', (ev) => {
      this.currentIndex = ev.to;
    });
  },
  methods: {
    changeSlide(index) {
      if (this.myCarousel) {
        this.myCarousel.to(index);
        this.currentIndex = index;
      }
    },
  },
};
</script>

<style scoped>
/* Hero image ocupa todo el ancho del móvil y mantiene proporción */
.hero-img-wrapper {
  width: 100%;
  padding-bottom: 100%; /* 1:1 aspect ratio en mobile */
  position: relative;
  background-size: contain;
  background-repeat: no-repeat;
  background-position: center;
  border-radius: 0.75rem;
  background-color: #f8f9fa;
  overflow: hidden;
}

@media (min-width: 768px) {
  /* relación 4:3 en desktop */
  .hero-img-wrapper {
    padding-bottom: 75%;
  }
}

/* Texto de “OFERTA” */
.oferta {
  position: absolute;
  top: 0.5rem;
  left: 0.5rem;
  padding: 0.25rem 0.5rem;
  font-size: 0.8rem;
  font-weight: 700;
  color: #fff;
  background-color: #dc3545; /* Bootstrap danger */
  border-radius: 0.25rem;
  z-index: 2;
}

/* Miniaturas */
.thumbs-wrapper {
  padding-top: 0.5rem;
  -webkit-overflow-scrolling: touch;
}

.thumb-btn {
  flex: 0 0 auto;
  border: 2px solid #dee2e6;
  border-radius: 0.5rem;
  padding: 0;
  background: #fff;
  width: 64px;
  height: 64px;
}

.thumb-btn.active {
  border-color: #ff7a00; /* color destacado */
}

.thumb-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 0.35rem;
}

/* Ajustes de flechas de navegación si deseas personalizarlas */
/*
.carousel-control-prev-icon,
.carousel-control-next-icon {
  filter: invert(1);
}
*/
</style>
