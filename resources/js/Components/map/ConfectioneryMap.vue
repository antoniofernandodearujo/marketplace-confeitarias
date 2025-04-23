<template>
  <div class="card">
    <div class="card-header">
      <h2 class="text-lg sm:text-xl font-semibold text-primary-900 flex items-center">
        <MapIcon class="w-5 h-5 sm:w-6 sm:h-6 mr-2 text-primary-700" />
        Mapa de Confeitarias
      </h2>
    </div>
    <div class="card-body p-3 sm:p-4">
      <!-- Altura fixa em dispositivos móveis, altura dinâmica em desktop -->
      <div id="map" ref="mapContainer" class="h-[300px] sm:h-[400px] lg:h-[calc(100vh-14rem)] w-full rounded-lg"></div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, watch } from 'vue';
import L from 'leaflet';
import type { Map, Marker, LatLngBounds, LeafletMouseEvent, Popup } from 'leaflet';
import 'leaflet/dist/leaflet.css';
import { MapIcon, PhoneIcon, MapPinIcon, CakeIcon } from '@heroicons/vue/24/outline';
import type { SwiperOptions } from 'swiper/types';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

const props = withDefaults(defineProps<{
  confectioneries: Array<{
    id: number;
    name: string;
    latitude: number;
    longitude: number;
    address: {
      street: string;
      number: string;
      neighborhood: string;
      city: string;
      state: string;
    };
    phone: string;
  }>;
  products: Array<{
    id: number;
    confectionery_id: number;
    name: string;
    price: number;
    images: Array<{ url: string }>;
  }>;
}>(), {
  confectioneries: () => [],
  products: () => []
});

const mapContainer = ref<HTMLElement | null>(null);
const map = ref<Map | null>(null);
const markers = ref<Marker[]>([]);

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  }).format(price);
};

const createPopupContent = (confectionery: any) => {
  const products = props.products.filter(p => p.confectionery_id === confectionery.id);
  const hasProducts = products.length > 0;
  
  let content = `
    <div class="popup-content">
      <h3 class="text-xl font-bold mb-4 text-primary-700 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7h18zm-3-9v-2a2 2 0 00-2-2H8a2 2 0 00-2 2v2h12z" />
        </svg>
        ${confectionery.name}
      </h3>
      <div class="space-y-2 mb-4">
        <p class="flex items-center text-primary-600">
          <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          ${confectionery.address.street}, ${confectionery.address.number}
        </p>
        <p class="ml-6 text-primary-600">${confectionery.address.neighborhood}</p>
        <p class="ml-6 text-primary-600">${confectionery.address.city}/${confectionery.address.state}</p>
        <p class="flex items-center text-primary-600">
          <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
          </svg>
          ${confectionery.phone}
        </p>
      </div>`;

  if (hasProducts) {
    content += `
      <div class="products-section">
        <h4 class="font-semibold text-primary-800 mb-2 flex items-center">
          <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
          Produtos (${products.length})
        </h4>
        <div class="swiper-container mb-4">
          <div class="swiper-wrapper">
            ${products.map(product => `
              <div class="swiper-slide">
                <div class="relative w-full h-48">
                  ${product.images && product.images.length > 0 ? `
                    <div class="product-image-placeholder absolute inset-0 bg-primary-100 animate-pulse"></div>
                    <img 
                      src="${product.images[0].url}" 
                      alt="${product.name}" 
                      class="product-image w-full h-full object-cover rounded-lg relative z-10"
                      data-loaded="false"
                    />
                  ` : `
                    <div class="w-full h-full flex items-center justify-center bg-primary-100 rounded-lg">
                      <svg class="w-12 h-12 text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                      </svg>
                    </div>
                  `}
                  <div class="p-2 bg-primary-800 bg-opacity-80 text-white absolute bottom-0 left-0 right-0 z-20">
                    <p class="font-semibold text-sm md:text-base">${product.name}</p>
                    <p class="text-xs md:text-sm">${formatPrice(product.price)}</p>
                  </div>
                </div>
              </div>
            `).join('')}
          </div>
          <div class="swiper-pagination"></div>
          <div class="swiper-button-prev hidden md:flex"></div>
          <div class="swiper-button-next hidden md:flex"></div>
        </div>
      </div>`;
  } else {
    content += `
      <div class="products-section">
         <h4 class="font-semibold text-primary-800 mb-2 flex items-center">
          <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
          Produtos
        </h4>
        <p class="text-primary-500 italic">Nenhum produto cadastrado para esta confeitaria.</p>
      </div>
    `;
  }

  content += '</div>';
  return content;
};

const updateMarkers = () => {
  if (!map.value) return;

  markers.value.forEach(marker => marker.remove());
  markers.value = [];

  const bounds = L.latLngBounds([]);

  props.confectioneries.forEach(confectionery => {
    if (!confectionery.latitude || !confectionery.longitude) return;

    const marker = L.marker([confectionery.latitude, confectionery.longitude])
      .bindPopup(createPopupContent(confectionery), {
        className: 'custom-popup',
        maxWidth: 400
      })
      .on('popupopen', async (e) => {
        const popup = e.popup;
        const container = popup.getElement();
        if (!container) return;
        
        // Inicializar o Swiper após abrir o popup
        const swiperContainer = container.querySelector('.swiper-container') as HTMLElement;
        if (swiperContainer) {
          await loadSwiper(swiperContainer);
        }
        
        // Corrigir a exibição das imagens
        const images = container.querySelectorAll('.product-image') as NodeListOf<HTMLImageElement>;
        images.forEach(img => {
          if (img.complete) {
            // Se a imagem já carregou, remove o placeholder
            const placeholder = img.previousElementSibling as HTMLElement;
            if (placeholder && placeholder.classList.contains('product-image-placeholder')) {
              placeholder.style.display = 'none';
            }
          } else {
            // Se não, adiciona um evento de carregamento
            img.onload = function() {
              const placeholder = img.previousElementSibling as HTMLElement;
              if (placeholder && placeholder.classList.contains('product-image-placeholder')) {
                placeholder.style.display = 'none';
              }
            };
          }
        });
      });

    marker.addTo(map.value as Map);
    markers.value.push(marker);
    bounds.extend([confectionery.latitude, confectionery.longitude]);
  });

  if (markers.value.length > 0) {
    map.value.fitBounds(bounds, { padding: [50, 50] });
  }
};

const initMap = () => {
  if (!mapContainer.value || map.value) return;

  const defaultCenter: [number, number] = [-15.77972, -47.92972]; // Centro do Brasil
  
  map.value = L.map(mapContainer.value).setView(defaultCenter, 4);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
  }).addTo(map.value as Map);

  updateMarkers();
};

const loadSwiper = async (container: HTMLElement) => {
  try {
    const Swiper = await import('swiper');
    const { Navigation, Pagination, Autoplay } = await import('swiper');
    
    const swiperOptions: SwiperOptions = {
      modules: [Navigation, Pagination, Autoplay],
      navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
      },
      pagination: {
        el: '.swiper-pagination',
        clickable: true,
        type: 'bullets',
      },
      autoplay: {
        delay: 3000,
        disableOnInteraction: false,
      },
      loop: true,
      slidesPerView: 1,
      speed: 500,
      observer: true,
      observeParents: true,
      watchSlidesProgress: true,
      updateOnWindowResize: true,
      grabCursor: true,
      effect: 'slide',
    };
    
    return new Swiper(container, swiperOptions);
  } catch (error) {
    console.error('Erro ao carregar o Swiper:', error);
    return null;
  }
};

watch(() => props.confectioneries, updateMarkers, { deep: true });
watch(() => props.products, updateMarkers, { deep: true });

onMounted(() => {
  initMap();
});

onBeforeUnmount(() => {
  if (map.value) {
    map.value.remove();
    map.value = null;
  }
});
</script>

<style lang="postcss">
.custom-popup .leaflet-popup-content-wrapper {
  min-width: 280px;
  max-width: 400px;
  @apply rounded-lg shadow-lg bg-white;
}

.custom-popup .leaflet-popup-content {
  width: 100% !important;
  margin: 0;
  @apply font-sans p-4;
}

.custom-popup .leaflet-popup-close-button {
  @apply p-2 text-primary-700 hover:text-primary-900;
}

.swiper-container {
  @apply relative overflow-hidden rounded-lg;
  width: 100%;
  height: 100%;
}

.swiper {
  width: 100%;
  height: 100%;
}

.swiper-slide {
  @apply flex justify-center items-center;
  width: 100% !important;
  height: auto !important;
}

.swiper-button-next,
.swiper-button-prev {
  @apply text-white bg-primary-800 bg-opacity-60 rounded-full w-10 h-10 flex items-center justify-center hover:bg-opacity-80 transition-all duration-300;
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  z-index: 30;
  cursor: pointer;
}

.swiper-button-next::after,
.swiper-button-prev::after {
  @apply text-base font-bold;
  font-size: 1.2rem !important;
  width: auto;
  height: auto;
  font-family: inherit;
}

.swiper-button-next::after {
  content: '›';
  margin-left: 2px;
}

.swiper-button-prev::after {
  content: '‹';
  margin-right: 2px;
}

.swiper-button-next {
  right: 10px;
}

.swiper-button-prev {
  left: 10px;
}

.swiper-button-next:hover,
.swiper-button-prev:hover {
  @apply bg-primary-700 bg-opacity-80;
}

.swiper-button-disabled {
  @apply opacity-30 cursor-not-allowed;
  pointer-events: none;
}

.swiper-pagination-bullet {
  @apply bg-white bg-opacity-70;
}

.swiper-pagination-bullet-active {
  @apply bg-white;
}

@media (max-width: 640px) {
  .custom-popup .leaflet-popup-content-wrapper {
    min-width: 250px;
    @apply max-w-[90vw];
  }
}

/* Garantir que o Swiper e seus slides sejam visíveis */
.swiper,
.swiper-wrapper,
.swiper-slide {
  visibility: visible !important;
  opacity: 1 !important;
}
</style>