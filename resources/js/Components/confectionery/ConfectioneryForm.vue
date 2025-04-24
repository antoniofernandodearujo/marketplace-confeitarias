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
            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
            required
          />
          <span v-if="v$.name.$error" class="text-xs text-red-500">{{ v$.name.$errors[0].$message }}</span>
        </div>

        <!-- Telefone -->
        <div>
          <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
          <div class="w-full">
            <input 
              type="tel" 
              id="phone" 
              v-model="form.phone"
              v-imask="{ mask: '(00) 00000-0000' }"
              :class="{'border-red-500': v$.phone.$error}"
              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
              placeholder="(00) 00000-0000"
            />
          </div>
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
              v-imask="{ mask: '00000-000' }"
              :class="{'border-red-500': v$.address.cep.$error}"
              class="flex-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
              placeholder="00000-000"
              @accept="searchAddress"
            />
            <button 
              type="button"
              @click="searchAddress"
              :disabled="isLoadingAddress || !form.address.cep"
              class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50"
            >
              <span v-if="!isLoadingAddress">Buscar</span>
              <span v-else>Buscando...</span>
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
            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
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
            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
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
            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
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
            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
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
            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
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
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
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
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                @blur="v$.longitude.$touch()"
                required
              />
              <span v-if="v$.longitude.$error && v$.longitude.$dirty" class="text-xs text-red-500">Coordenada inválida</span>
            </div>
            <div class="flex items-end">
              <button 
                type="button"
                @click="getCurrentLocation"
                class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700"
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
          class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50"
        >
          {{ isSubmitting ? 'Salvando...' : (isEditing ? 'Atualizar' : 'Cadastrar') }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
/**
 * Componente ConfectioneryForm
 * 
 * Formulário para criação e edição de confeitarias.
 * Inclui:
 * - Dados básicos da confeitaria
 * - Endereço completo com busca automática por CEP
 * - Seleção de localização geográfica via mapa interativo
 */
import { ref, reactive, onMounted, onBeforeUnmount, computed } from 'vue';
import { useVuelidate } from '@vuelidate/core';
import { required, helpers, minLength, maxLength } from '@vuelidate/validators';
import L from 'leaflet';
import { ConfectioneryAPI, AddressAPI } from '@/services/api';
import { IMask } from 'vue-imask';
import { useToast } from 'vue-toast-notification';
import type { Confectionery, Address } from '@/types/';

// Sistema de notificações toast
const toast = useToast();

/**
 * Interface para os dados do formulário
 */
interface FormData {
  name: string;
  phone: string;
  latitude: string | null;
  longitude: string | null;
  address: {
    cep: string;
    street: string;
    number: string;
    neighborhood: string;
    city: string;
    state: string;
  }
}

/**
 * Validador personalizado para coordenadas geográficas
 * 
 * @param value Valor da coordenada a validar
 * @returns boolean Indica se a coordenada é válida
 */
const isValidCoordinate = (value: string | null | undefined): boolean => {
  if (value === null || value === undefined || value === '') return false;
  const number = Number(value);
  return !isNaN(number) && Boolean(String(number).match(/^-?\d+\.?\d*$/));
};

// Props do componente
interface Props {
  initialData?: Confectionery & { address: Address };
  isEditing?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  initialData: undefined,
  isEditing: false
});

// Eventos emitidos pelo componente
const emit = defineEmits<{
  (e: 'submit', data: Confectionery): void;
  (e: 'cancel'): void;
}>();

/**
 * Estado reativo do formulário
 */
const form = reactive<FormData>({
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

/**
 * Regras de validação do formulário
 */
const rules = {
  name: { 
    required: helpers.withMessage('O nome é obrigatório', required) 
  },
  phone: { 
    required: helpers.withMessage('O telefone é obrigatório', required), 
    minLength: helpers.withMessage('Telefone inválido', minLength(10)) 
  },
  address: {
    cep: { 
      required: helpers.withMessage('O CEP é obrigatório', required),
      minLength: helpers.withMessage('CEP inválido', minLength(8)) 
    },
    street: { required: helpers.withMessage('A rua é obrigatória', required) },
    number: { required: helpers.withMessage('O número é obrigatório', required) },
    neighborhood: { required: helpers.withMessage('O bairro é obrigatório', required) },
    city: { required: helpers.withMessage('A cidade é obrigatória', required) }, 
    state: { 
      required: helpers.withMessage('O estado é obrigatório', required), 
      minLength: helpers.withMessage('Use a sigla do estado (2 letras)', minLength(2)), 
      maxLength: helpers.withMessage('Use a sigla do estado (2 letras)', maxLength(2)) 
    }
  },
  latitude: { 
    required: helpers.withMessage('A latitude é obrigatória', required),
    valid: helpers.withMessage('Coordenada inválida', isValidCoordinate)
  },
  longitude: { 
    required: helpers.withMessage('A longitude é obrigatória', required),
    valid: helpers.withMessage('Coordenada inválida', isValidCoordinate)
  }
};

// Instância de validação
const v$ = useVuelidate(rules, form);

// Estado do componente
const isSubmitting = ref(false);
const isLoadingAddress = ref(false);
const addressError = ref('');
const locationMap = ref<HTMLElement | null>(null);
const map = ref<L.Map | null>(null);
const marker = ref<L.Marker | null>(null);

/**
 * Coordenadas do Brasil para centralização inicial do mapa
 */
const BRAZIL_COORDS = {
  lat: -15.77972,
  lng: -47.92972,
  zoom: 4
};

/**
 * Coordenadas atuais para o mapa (computed para evitar NaN)
 */
const currentCoords = computed(() => {
  const lat = form.latitude ? parseFloat(form.latitude) : BRAZIL_COORDS.lat;
  const lng = form.longitude ? parseFloat(form.longitude) : BRAZIL_COORDS.lng;
  
  // Retorna apenas coordenadas válidas
  if (isNaN(lat) || isNaN(lng)) {
    return { lat: BRAZIL_COORDS.lat, lng: BRAZIL_COORDS.lng };
  }
  
  return { lat, lng };
});

/**
 * Inicializa o mapa Leaflet quando o componente é montado
 */
onMounted(() => {
  if (locationMap.value) {
    // Inicializa o mapa com as coordenadas do Brasil por padrão
    map.value = L.map(locationMap.value).setView(
      [BRAZIL_COORDS.lat, BRAZIL_COORDS.lng], 
      BRAZIL_COORDS.zoom
    );
    
    // Adiciona a camada base do OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors',
      maxZoom: 19
    }).addTo(map.value as L.Map);

    // Configura o evento de clique no mapa para atualizar coordenadas
    map.value.on('click', (e: L.LeafletMouseEvent) => {
      form.latitude = e.latlng.lat.toString();
      form.longitude = e.latlng.lng.toString();
      updateMarker();
    });

    // Se estiver editando, carrega os dados iniciais
    if (props.initialData) {
      // Preenche o formulário com os dados existentes
      form.name = props.initialData.name;
      form.phone = props.initialData.phone;
      form.latitude = props.initialData.latitude?.toString() || null;
      form.longitude = props.initialData.longitude?.toString() || null;
      
      if (props.initialData.address) {
        form.address = {
          cep: props.initialData.address.cep || '',
          street: props.initialData.address.street || '',
          number: props.initialData.address.number || '',
          neighborhood: props.initialData.address.neighborhood || '',
          city: props.initialData.address.city || '',
          state: props.initialData.address.state || ''
        };
      }
      
      // Atualiza o marcador no mapa
      updateMarker();
    }
  }
});

/**
 * Limpa recursos do mapa quando o componente é desmontado
 */
onBeforeUnmount(() => {
  if (map.value) {
    map.value.remove();
  }
});

/**
 * Atualiza a posição do marcador no mapa 
 * baseado nas coordenadas atuais do formulário
 */
const updateMarker = () => {
  if (!map.value) return;

  const coords = currentCoords.value;

  // Remove o marcador existente se houver
  if (marker.value) {
    marker.value.remove();
  }

  // Adiciona novo marcador com as coordenadas atuais
  marker.value = L.marker([coords.lat, coords.lng]);
  marker.value.addTo(map.value as L.Map);
  
  // Centraliza o mapa nas coordenadas com zoom adequado
  (map.value as L.Map).setView([coords.lat, coords.lng], 15);
};

/**
 * Busca informações de endereço a partir do CEP
 */
const searchAddress = async () => {
  // Valida se o CEP foi informado
  if (!form.address.cep || form.address.cep.length < 8) {
    addressError.value = 'Informe um CEP válido';
    return;
  }

  isLoadingAddress.value = true;
  addressError.value = '';

  try {
    // Remove caracteres não numéricos do CEP
    const cepNumerico = form.address.cep.replace(/\D/g, '');
    
    // Consulta o endereço pela API
    const response = await AddressAPI.fetchByCep(cepNumerico);
    const data = response.data;

    // Preenche o formulário com os dados retornados
    form.address.street = data.street || '';
    form.address.neighborhood = data.neighborhood || '';
    form.address.city = data.city || '';
    form.address.state = data.state || '';

    // Busca as coordenadas geográficas do endereço
    try {
      const searchQuery = `${data.street}, ${data.city}, ${data.state}, Brazil`;
      const geoResponse = await fetch(
        `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(searchQuery)}`
      ).then(res => res.json());

      if (geoResponse && geoResponse.length > 0) {
        form.latitude = geoResponse[0].lat;
        form.longitude = geoResponse[0].lon;
        updateMarker();
      }
    } catch (geoError) {
      console.error('Erro ao buscar coordenadas:', geoError);
      // Não exibe erro para o usuário, apenas no console
      // pois as coordenadas podem ser inseridas manualmente
    }
  } catch (error) {
    addressError.value = 'CEP não encontrado ou serviço indisponível';
    console.error('Erro ao buscar CEP:', error);
  } finally {
    isLoadingAddress.value = false;
  }
};

/**
 * Obtém a localização atual do usuário usando Geolocation API
 */
const getCurrentLocation = () => {
  if (!navigator.geolocation) {
    toast.error('Geolocalização não suportada pelo seu navegador');
    return;
  }

  toast.info('Obtendo sua localização atual...');

  navigator.geolocation.getCurrentPosition(
    (position) => {
      form.latitude = position.coords.latitude.toString();
      form.longitude = position.coords.longitude.toString();
      updateMarker();
      toast.success('Localização obtida com sucesso!');
    },
    (error) => {
      let message = 'Erro ao obter localização';
      switch (error.code) {
        case 1: message = 'Permissão negada para acessar sua localização'; break;
        case 2: message = 'Não foi possível determinar sua localização atual'; break;
        case 3: message = 'Tempo esgotado ao tentar obter localização'; break;
      }
      toast.error(message);
    },
    { 
      enableHighAccuracy: true, 
      timeout: 10000,
      maximumAge: 0
    }
  );
};

/**
 * Envia o formulário para criar ou atualizar uma confeitaria
 */
const handleSubmit = async () => {
  // Valida o formulário
  const valid = await v$.value.$validate();
  if (!valid) return;

  isSubmitting.value = true;

  try {
    // Prepara os dados para envio
    const formData = {
      name: form.name,
      phone: form.phone.replace(/\D/g, ''), // Remove caracteres não-numéricos
      latitude: form.latitude !== null ? parseFloat(form.latitude) : null,
      longitude: form.longitude !== null ? parseFloat(form.longitude) : null,
      address: {
        cep: form.address.cep.replace(/\D/g, ''),
        street: form.address.street,
        number: form.address.number,
        neighborhood: form.address.neighborhood,
        city: form.address.city,
        state: form.address.state.toUpperCase() // Garante que o estado esteja em maiúsculas
      }
    };

    let response;
    if (props.isEditing && props.initialData?.id) {
      // Atualiza uma confeitaria existente
      response = await ConfectioneryAPI.update(props.initialData.id, formData);
      toast.success(`Confeitaria ${form.name} atualizada com sucesso!`, {
        position: 'top-right',
        duration: 3000
      });
    } else {
      // Cria uma nova confeitaria
      response = await ConfectioneryAPI.create(formData);
      toast.success(`Confeitaria ${form.name} cadastrada com sucesso!`, {
        position: 'top-right',
        duration: 3000
      });
    }

    // Emite evento de sucesso com os dados retornados
    emit('submit', response.data);
  } catch (error: any) {
    console.error('Erro ao salvar confeitaria:', error);
    const errorMessage = error.response?.data?.error || 'Erro ao salvar confeitaria. Por favor, tente novamente.';
    toast.error(errorMessage, {
      position: 'top-right',
      duration: 5000
    });
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
