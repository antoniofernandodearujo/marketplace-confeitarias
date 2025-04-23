<template>
  <div class="bg-white rounded-lg shadow-lg p-6">
    <h3 class="text-xl font-semibold mb-6">{{ isEditing ? 'Editar' : 'Nova' }} Confeitaria</h3>
    
    <form @submit.prevent="handleSubmit" class="space-y-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Nome -->
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome do Estabelecimento</label>
          <input 
            type="text" 
            id="name" 
            v-model="form.name" 
            :class="{'border-red-500': v$.name.$error}"
            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            required
          />
          <span v-if="v$.name.$error" class="text-xs text-red-500">{{ v$.name.$errors[0].$message }}</span>
        </div>

        <!-- Telefone -->
        <div>
          <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
          <input 
            type="tel" 
            id="phone" 
            v-model="form.phone"
            :class="{'border-red-500': v$.phone.$error}"
            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            required
          />
          <span v-if="v$.phone.$error" class="text-xs text-red-500">{{ v$.phone.$errors[0].$message }}</span>
        </div>

        <!-- CEP -->
        <div>
          <label for="cep" class="block text-sm font-medium text-gray-700 mb-1">CEP</label>
          <div class="flex space-x-2">
            <input 
              type="text" 
              id="cep" 
              v-model="form.address.cep"
              :class="{'border-red-500': v$.address.cep.$error}"
              class="flex-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              required
            />
            <button 
              type="button" 
              @click="searchAddress"
              :disabled="isLoadingAddress"
              class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
            >
              {{ isLoadingAddress ? 'Buscando...' : 'Buscar' }}
            </button>
          </div>
          <span v-if="v$.address.cep.$error" class="text-xs text-red-500">{{ v$.address.cep.$errors[0].$message }}</span>
          <span v-if="addressError" class="text-xs text-red-500">{{ addressError }}</span>
        </div>

        <!-- Rua -->
        <div>
          <label for="street" class="block text-sm font-medium text-gray-700 mb-1">Rua</label>
          <input 
            type="text" 
            id="street" 
            v-model="form.address.street"
            :class="{'border-red-500': v$.address.street.$error}"
            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            required
          />
          <span v-if="v$.address.street.$error" class="text-xs text-red-500">{{ v$.address.street.$errors[0].$message }}</span>
        </div>

        <!-- Número -->
        <div>
          <label for="number" class="block text-sm font-medium text-gray-700 mb-1">Número</label>
          <input 
            type="text" 
            id="number" 
            v-model="form.address.number"
            :class="{'border-red-500': v$.address.number.$error}"
            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            required
          />
          <span v-if="v$.address.number.$error" class="text-xs text-red-500">{{ v$.address.number.$errors[0].$message }}</span>
        </div>

        <!-- Bairro -->
        <div>
          <label for="neighborhood" class="block text-sm font-medium text-gray-700 mb-1">Bairro</label>
          <input 
            type="text" 
            id="neighborhood" 
            v-model="form.address.neighborhood"
            :class="{'border-red-500': v$.address.neighborhood.$error}"
            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            required
          />
          <span v-if="v$.address.neighborhood.$error" class="text-xs text-red-500">{{ v$.address.neighborhood.$errors[0].$message }}</span>
        </div>

        <!-- Cidade -->
        <div>
          <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
          <input 
            type="text" 
            id="city" 
            v-model="form.address.city"
            :class="{'border-red-500': v$.address.city.$error}"
            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            required
          />
          <span v-if="v$.address.city.$error" class="text-xs text-red-500">{{ v$.address.city.$errors[0].$message }}</span>
        </div>

        <!-- Estado -->
        <div>
          <label for="state" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
          <input 
            type="text" 
            id="state" 
            v-model="form.address.state"
            :class="{'border-red-500': v$.address.state.$error}"
            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            required
          />
          <span v-if="v$.address.state.$error" class="text-xs text-red-500">{{ v$.address.state.$errors[0].$message }}</span>
        </div>
      </div>

      <!-- Mapa para seleção de coordenadas -->
      <div class="mt-6">
        <label class="block text-sm font-medium text-gray-700 mb-1">Localização no Mapa</label>
        <div class="bg-gray-100 rounded-lg p-4">
          <div class="flex space-x-4 mb-4">
            <div class="flex-1">
              <label for="latitude" class="block text-xs text-gray-600 mb-1">Latitude</label>
              <input 
                type="text" 
                id="latitude" 
                v-model="form.latitude"
                :class="{'border-red-500': v$.latitude.$error && v$.latitude.$dirty}"
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                @blur="v$.latitude.$touch()"
                required
              />
              <span v-if="v$.latitude.$error && v$.latitude.$dirty" class="text-xs text-red-500">Coordenada inválida</span>
            </div>
            <div class="flex-1">
              <label for="longitude" class="block text-xs text-gray-600 mb-1">Longitude</label>
              <input 
                type="text" 
                id="longitude" 
                v-model="form.longitude"
                :class="{'border-red-500': v$.longitude.$error && v$.longitude.$dirty}"
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                @blur="v$.longitude.$touch()"
                required
              />
              <span v-if="v$.longitude.$error && v$.longitude.$dirty" class="text-xs text-red-500">Coordenada inválida</span>
            </div>
            <div class="flex items-end">
              <button 
                type="button"
                @click="getCurrentLocation"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
              >
                Usar Minha Localização
              </button>
            </div>
          </div>
          <div ref="locationMap" class="h-[300px] rounded-lg"></div>
        </div>
      </div>

      <!-- Botões -->
      <div class="flex justify-end space-x-3 pt-6">
        <button 
          type="button" 
          @click="$emit('cancel')"
          class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
        >
          Cancelar
        </button>
        <button 
          type="submit"
          :disabled="isSubmitting"
          class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
        >
          {{ isSubmitting ? 'Salvando...' : (isEditing ? 'Atualizar' : 'Cadastrar') }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, onBeforeUnmount } from 'vue';
import { useVuelidate } from '@vuelidate/core';
import { required, helpers, minLength, maxLength } from '@vuelidate/validators';
import L from 'leaflet';
import axios from 'axios';
import { ConfectioneryAPI } from '@/services/api';

// Validador personalizado para coordenadas
const isValidCoordinate = (value) => {
  if (value === null || value === undefined || value === '') return false;
  const number = Number(value);
  return !isNaN(number) && String(number).match(/^-?\d+\.?\d*$/);
};

const props = defineProps<{
  initialData?: any;
  isEditing?: boolean;
}>();

const emit = defineEmits<{
  (e: 'submit', data?: any): void;
  (e: 'cancel'): void;
}>();

const form = reactive({
  name: '',
  phone: '',
  latitude: null,
  longitude: null,
  address: {
    cep: '',
    street: '',
    number: '',
    neighborhood: '',
    city: '',
    state: ''
  }
});

// Validação
const rules = {
  name: { required },
  phone: { required, minLength: minLength(10) },
  address: {
    cep: { required, minLength: minLength(8) },
    street: { required },
    number: { required },
    neighborhood: { required },
    city: { required }, 
    state: { required, minLength: minLength(2), maxLength: maxLength(2) }
  },
  latitude: { 
    required,
    valid: helpers.withMessage('Coordenada inválida', isValidCoordinate)
  },
  longitude: { 
    required,
    valid: helpers.withMessage('Coordenada inválida', isValidCoordinate)
  }
};

const v$ = useVuelidate(rules, form);

// Estado
const isSubmitting = ref(false);
const isLoadingAddress = ref(false);
const addressError = ref('');
const locationMap = ref<HTMLElement | null>(null);
const map = ref<L.Map | null>(null);
const marker = ref<L.Marker | null>(null);

// Inicializar mapa
onMounted(() => {
  if (locationMap.value) {
    map.value = L.map(locationMap.value).setView([-15.77972, -47.92972], 4);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors'
    }).addTo(map.value as L.Map);

    map.value.on('click', (e: L.LeafletMouseEvent) => {
      form.latitude = parseFloat(e.latlng.lat.toString());
      form.longitude = parseFloat(e.latlng.lng.toString());
      updateMarker();
    });

    if (props.initialData) {
      Object.assign(form, props.initialData);
      updateMarker();
    }
  }
});

onBeforeUnmount(() => {
  if (map.value) {
    map.value.remove();
  }
});

// Métodos
const updateMarker = () => {
  if (!map.value || form.latitude === null || form.longitude === null) return;

  if (marker.value) {
    marker.value.remove();
  }

  const lat = parseFloat(form.latitude);
  const lng = parseFloat(form.longitude);
  
  if (isNaN(lat) || isNaN(lng)) return;
  
  marker.value = L.marker([lat, lng]).addTo(map.value);
  map.value.setView([lat, lng], 15);
};

const searchAddress = async () => {
  if (!form.address.cep) return;

  isLoadingAddress.value = true;
  addressError.value = '';

  try {
    const response = await axios.get(`https://viacep.com.br/ws/${form.address.cep}/json/`);
    const data = response.data;

    if (data.erro) {
      addressError.value = 'CEP não encontrado';
      return;
    }

    form.address.street = data.logradouro;
    form.address.neighborhood = data.bairro;
    form.address.city = data.localidade;
    form.address.state = data.uf;

    // Buscar coordenadas
    const geoResponse = await axios.get(
      `https://nominatim.openstreetmap.org/search`,
      {
        params: {
          street: data.logradouro,
          city: data.localidade,
          state: data.uf,
          country: 'Brazil',
          format: 'json'
        }
      }
    );

    if (geoResponse.data.length > 0) {
      form.latitude = parseFloat(geoResponse.data[0].lat);
      form.longitude = parseFloat(geoResponse.data[0].lon);
      updateMarker();
    }
  } catch (error) {
    addressError.value = 'Erro ao buscar endereço';
    console.error('Erro:', error);
  } finally {
    isLoadingAddress.value = false;
  }
};

const getCurrentLocation = () => {
  if (!navigator.geolocation) {
    alert('Geolocalização não suportada pelo navegador');
    return;
  }

  navigator.geolocation.getCurrentPosition(
    (position) => {
      form.latitude = parseFloat(position.coords.latitude.toString());
      form.longitude = parseFloat(position.coords.longitude.toString());
      updateMarker();
    },
    (error) => {
      alert('Erro ao obter localização: ' + error.message);
    }
  );
};

const handleSubmit = async () => {
  const valid = await v$.value.$validate();
  if (!valid) return;

  isSubmitting.value = true;

  try {
    const formData = {
      name: form.name,
      phone: form.phone,
      latitude: form.latitude !== null ? parseFloat(form.latitude) : null,
      longitude: form.longitude !== null ? parseFloat(form.longitude) : null,
      address: {
        cep: form.address.cep,
        street: form.address.street,
        number: form.address.number,
        neighborhood: form.address.neighborhood,
        city: form.address.city,
        state: form.address.state
      }
    };

    let response;
    if (props.isEditing && props.initialData?.id) {
      response = await ConfectioneryAPI.update(props.initialData.id, formData);
    } else {
      response = await ConfectioneryAPI.create(formData);
    }

    emit('submit', response?.data);
  } catch (error) {
    console.error('Erro ao salvar confeitaria:', error);
    alert('Erro ao salvar confeitaria. Por favor, tente novamente.');
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<style scoped>
.leaflet-container {
  z-index: 1;
}
</style>