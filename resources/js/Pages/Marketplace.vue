<template>
  <div class="min-h-screen bg-primary-50 flex flex-col">
    <div class="flex-grow">
      <div class="py-6">
        <div class="bg-white shadow-sm">
          <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Cabeçalho responsivo -->
            <div class="py-4 md:py-6 flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0">
              <!-- Título e barra de pesquisa em uma linha em desktop, empilhados em mobile -->
              <div class="flex-1 flex flex-col sm:flex-row items-start sm:items-center space-y-4 sm:space-y-0 sm:space-x-6">
                <h1 class="text-2xl sm:text-3xl font-bold text-primary-900 whitespace-nowrap">Marketplace de Confeitarias</h1>
                
                <!-- Campo de busca -->
                <div class="relative w-full sm:max-w-md">
                  <input
                    type="search"
                    v-model="searchQuery"
                    placeholder="Buscar confeitarias ou produtos..."
                    @input="handleSearch"
                    class="w-full pl-10 pr-4 py-2 border border-primary-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-700 input"
                  />
                  <div class="absolute left-3 top-2.5 text-primary-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                  </div>
                </div>
              </div>
              
              <!-- Botão sempre visível e alinhado corretamente -->
              <div class="flex justify-end">
                <button
                  @click="showConfectioneryForm = true"
                  class="btn btn-primary flex items-center"
                >
                  <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                  </svg>
                  <span class="hidden sm:inline">Nova Confeitaria</span>
                  <span class="sm:hidden">Adicionar</span>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Conteúdo Principal -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
          <!-- Grid Layout -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-8">
            <!-- Mapa - altura fixa em mobile, altura dinâmica em desktop -->
            <div class="order-2 lg:order-1 lg:sticky lg:top-6 lg:h-[calc(100vh-10rem)]">
              <ConfectioneryMap
                :confectioneries="filteredConfectioneries"
                :products="filteredProducts"
              />
            </div>

            <!-- Lista de Confeitarias -->
            <div class="order-1 lg:order-2 mb-6 lg:mb-0">
              <ConfectioneryList
                :confectioneries="filteredConfectioneries as any[]"
                :products="filteredProducts"
                :can-edit="true"
                :can-delete="true"
                :can-add-products="true"
                @edit-confectionery="editConfectionery"
                @delete-confectionery="deleteConfectionery"
                @add-product="addProduct"
                @edit-product="editProduct"
                @delete-product="deleteProduct"
                @refresh="fetchData"
              />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Adicionar o Footer -->
    <Footer />

    <!-- Modal de Confeitaria -->
    <Teleport to="body">
      <div v-if="showConfectioneryForm" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-screen items-center justify-center p-4">
          <div class="fixed inset-0 bg-primary-900 bg-opacity-75" @click="closeConfectioneryForm"></div>
          <div class="relative bg-white rounded-lg max-w-3xl w-full">
            <ConfectioneryForm
              :initial-data="selectedConfectionery"
              :is-editing="!!selectedConfectionery"
              @submit="handleConfectionerySubmit"
              @cancel="closeConfectioneryForm"
            />
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Modal de Produto -->
    <Teleport to="body">
      <div v-if="showProductForm" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-screen items-center justify-center p-4">
          <div class="fixed inset-0 bg-primary-900 bg-opacity-75" @click="closeProductForm"></div>
          <div class="relative bg-white rounded-lg max-w-3xl w-full">
            <ProductForm
              :initial-data="selectedProduct"
              :is-editing="!!selectedProduct?.id"
              :confectionery-id="selectedProduct?.confectionery_id"
              @submit="handleProductSubmit"
              @cancel="closeProductForm"
            />
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import ConfectioneryMap from '@/Components/map/ConfectioneryMap.vue';
import ConfectioneryList from '@/Components/confectionery/ConfectioneryList.vue';
import ConfectioneryForm from '@/Components/confectionery/ConfectioneryForm.vue';
import ProductForm from '@/Components/product/ProductForm.vue';
import Footer from '@/Components/shared/Footer.vue';
import { ConfectioneryAPI } from '@/services/api';
import { useToast } from 'vue-toast-notification';

const toast = useToast();

interface Address {
  street: string;
  number: string;
  neighborhood: string;
  city: string;
  state: string;
}

interface Confectionery {
  id: number;
  name: string;
  phone: string;
  address: Address;
  latitude: number;
  longitude: number;
}

interface ConfectioneryListItem {
  id: number;
  name: string;
  phone: string;
  address: Address;
}

interface Product {
  id: number;
  confectionery_id: number;
  name: string;
  price: number;
  description: string;
  images: Array<{ url: string }>;
}

interface Props {
  initialConfectioneries: Confectionery[];
  initialProducts: Product[];
}

const props = withDefaults(defineProps<Props>(), {
  initialConfectioneries: () => [],
  initialProducts: () => []
});

const confectioneries = ref<Confectionery[]>(props.initialConfectioneries || []);
const products = ref<Product[]>(props.initialProducts || []);
const showConfectioneryForm = ref(false);
const showProductForm = ref(false);
const selectedConfectionery = ref<Confectionery | null>(null);
const selectedProduct = ref<Product | null>(null);
const searchQuery = ref('');

// Computed properties para filtrar confeitarias e produtos
const filteredConfectioneries = computed(() => {
  if (!searchQuery.value) return confectioneries.value;
  
  const query = searchQuery.value.toLowerCase();
  return confectioneries.value.filter(conf => 
    conf.name.toLowerCase().includes(query) || 
    products.value.some(p => 
      p.confectionery_id === conf.id && 
      p.name.toLowerCase().includes(query)
    )
  );
});

const filteredProducts = computed(() => {
  if (!searchQuery.value) return products.value;
  
  const query = searchQuery.value.toLowerCase();
  return products.value.filter(product => 
    product.name.toLowerCase().includes(query) || 
    confectioneries.value.some(conf => 
      conf.id === product.confectionery_id && 
      conf.name.toLowerCase().includes(query)
    )
  );
});

// Methods
const handleSearch = () => {
  // A filtragem é feita automaticamente através das computed properties
};

const fetchData = async () => {
  try {
    const response = await ConfectioneryAPI.getAll();
    confectioneries.value = response.data.confectioneries;
    products.value = response.data.products;
    toast.success('Dados atualizados com sucesso!', {
      position: 'top-right',
      duration: 3000
    });
  } catch (error) {
    console.error('Erro ao carregar dados:', error);
    toast.error('Erro ao carregar dados. Por favor, recarregue a página.', {
      position: 'top-right',
      duration: 5000
    });
  }
};

const editConfectionery = (confectionery: ConfectioneryListItem | Confectionery) => {
  // Certifica-se de que temos todas as propriedades necessárias
  selectedConfectionery.value = confectionery as Confectionery;
  showConfectioneryForm.value = true;
};

const addProduct = (confectionery: ConfectioneryListItem | Confectionery) => {
  selectedProduct.value = {
    id: 0,
    confectionery_id: confectionery.id,
    name: '',
    price: 0,
    description: '',
    images: []
  };
  showProductForm.value = true;
};

const editProduct = (product: Product) => {
  selectedProduct.value = product;
  showProductForm.value = true;
};

const deleteConfectionery = () => {
  fetchData();
};

const deleteProduct = () => {
  fetchData();
};

const handleConfectionerySubmit = async () => {
  await fetchData();
  closeConfectioneryForm();
};

const handleProductSubmit = async () => {
  await fetchData();
  closeProductForm();
};

const closeConfectioneryForm = () => {
  showConfectioneryForm.value = false;
  selectedConfectionery.value = null;
};

const closeProductForm = () => {
  showProductForm.value = false;
  selectedProduct.value = null;
};

onMounted(() => {
  fetchData();
});
</script>