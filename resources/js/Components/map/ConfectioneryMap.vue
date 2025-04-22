<template>
  <div class="bg-white rounded-lg shadow-lg overflow-hidden">
    <div class="p-4 border-b border-gray-200">
      <h2 class="text-xl font-semibold text-gray-800">Mapa de Confeitarias</h2>
    </div>
    <div class="p-4">
      <div id="map" ref="mapContainer" class="h-[600px] w-full rounded-lg"></div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, watch } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

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
  }>;
}>(), {
  confectioneries: () => [],
  products: () => []
});

const mapContainer = ref<HTMLElement | null>(null);
const map = ref<L.Map | null>(null);
const markers = ref<L.Marker[]>([]);

const initMap = () => {
  if (!mapContainer.value || map.value) return;

  // Default to Brazil's center if no confectioneries
  const defaultCenter = [-15.77972, -47.92972];
  
  map.value = L.map(mapContainer.value).setView(defaultCenter, 4);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
  }).addTo(map.value);

  updateMarkers();
};

const updateMarkers = () => {
  if (!map.value) return;

  // Clear existing markers
  markers.value.forEach(marker => marker.remove());
  markers.value = [];

  // Add new markers
  const bounds = L.latLngBounds([]);

  props.confectioneries.forEach(confectionery => {
    if (!confectionery.latitude || !confectionery.longitude) return;

    const marker = L.marker([confectionery.latitude, confectionery.longitude])
      .bindPopup(`
        <div class="p-3">
          <h3 class="font-bold text-lg mb-2">${confectionery.name}</h3>
          <p class="text-sm mb-1">${confectionery.address.street}, ${confectionery.address.number}</p>
          <p class="text-sm mb-1">${confectionery.address.neighborhood}</p>
          <p class="text-sm mb-1">${confectionery.address.city}/${confectionery.address.state}</p>
          <p class="text-sm mb-2">${confectionery.phone}</p>
          <p class="text-sm font-semibold">
            Produtos: ${props.products.filter(p => p.confectionery_id === confectionery.id).length}
          </p>
        </div>
      `, {
        className: 'custom-popup'
      });

    marker.addTo(map.value!);
    markers.value.push(marker);
    bounds.extend([confectionery.latitude, confectionery.longitude]);
  });

  // Adjust map view if there are markers
  if (markers.value.length > 0) {
    map.value.fitBounds(bounds, { padding: [50, 50] });
  }
};

watch(() => props.confectioneries, updateMarkers, { deep: true });

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

<style scoped>
.custom-popup .leaflet-popup-content-wrapper {
  @apply rounded-lg shadow-lg;
}

.custom-popup .leaflet-popup-content {
  @apply m-0 font-sans;
}

.custom-popup .leaflet-popup-close-button {
  @apply p-2;
}
</style>