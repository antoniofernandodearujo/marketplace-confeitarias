<script setup lang="ts">
import { ref, onMounted } from 'vue';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';
import { Swiper, SwiperSlide } from 'swiper/vue';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

interface Product {
    id: number;
    name: string;
    price: number;
    description: string;
    images: string[];
}

interface Confectionery {
    id: number;
    name: string;
    latitude: number;
    longitude: number;
    address: string;
    phone: string;
    products: Product[];
}

const map = ref<L.Map | null>(null);
const selectedConfectionery = ref<Confectionery | null>(null);
const confectioneries = ref<Confectionery[]>([]);

onMounted(async () => {
    // Fix Leaflet default icon path issue
    delete (L.Icon.Default.prototype as any)._getIconUrl;
    L.Icon.Default.mergeOptions({
        iconRetinaUrl: '/marker-icon-2x.png',
        iconUrl: '/marker-icon.png',
        shadowUrl: '/marker-shadow.png',
    });

    // Initialize map
    map.value = L.map('map').setView([-23.5505, -46.6333], 13); // São Paulo coordinates as default

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map.value);

    // TODO: Fetch confectioneries data from API
    // For now, using mock data
    confectioneries.value = [];

    // Add markers for each confectionery
    confectioneries.value.forEach(conf => {
        const marker = L.marker([conf.latitude, conf.longitude])
            .addTo(map.value!)
            .bindPopup(conf.name)
            .on('click', () => {
                selectedConfectionery.value = conf;
            });
    });
});
</script>

<template>
    <div class="h-screen flex">
        <!-- Map -->
        <div id="map" class="w-2/3 h-full"></div>

        <!-- Sidebar with confectionery details -->
        <div class="w-1/3 h-full bg-white shadow-lg p-4 overflow-y-auto">
            <div v-if="selectedConfectionery" class="space-y-6">
                <h2 class="text-2xl font-bold">{{ selectedConfectionery.name }}</h2>
                
                <div class="space-y-2">
                    <p class="text-gray-600">
                        <span class="font-medium">Endereço:</span> {{ selectedConfectionery.address }}
                    </p>
                    <p class="text-gray-600">
                        <span class="font-medium">Telefone:</span> {{ selectedConfectionery.phone }}
                    </p>
                </div>

                <div class="space-y-4">
                    <h3 class="text-xl font-semibold">Produtos</h3>
                    
                    <div v-for="product in selectedConfectionery.products" :key="product.id" class="border rounded-lg p-4">
                        <h4 class="text-lg font-medium mb-2">{{ product.name }}</h4>
                        
                        <!-- Image Slider -->
                        <Swiper
                            :slides-per-view="1"
                            :pagination="{ clickable: true }"
                            :navigation="true"
                            class="h-48 mb-4"
                        >
                            <SwiperSlide v-for="(image, index) in product.images" :key="index">
                                <img :src="image" class="w-full h-full object-cover" :alt="product.name" />
                            </SwiperSlide>
                        </Swiper>

                        <p class="text-gray-600 mb-2">{{ product.description }}</p>
                        <p class="text-lg font-semibold text-indigo-600">
                            R$ {{ product.price.toFixed(2) }}
                        </p>
                    </div>
                </div>
            </div>

            <div v-else class="flex items-center justify-center h-full">
                <p class="text-gray-500 text-lg">
                    Selecione uma confeitaria no mapa para ver seus detalhes
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
:deep(.swiper-button-next),
:deep(.swiper-button-prev) {
    color: theme('colors.indigo.600');
}

:deep(.swiper-pagination-bullet-active) {
    background-color: theme('colors.indigo.600');
}
</style>