<template>
  <div class="min-h-screen bg-gray-100">
    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 flex justify-between items-center">
          <h1 class="text-3xl font-bold text-gray-900">Marketplace de Confeitarias</h1>
          <button
            @click="showConfectioneryForm = true"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
          >
            Nova Confeitaria
          </button>
        </div>

        <!-- Grid Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <!-- Mapa -->
          <div class="lg:sticky lg:top-6 lg:h-[calc(100vh-8rem)]">
            <ConfectioneryMap
              :confectioneries="confectioneries"
              :products="products"
            />
          </div>

          <!-- Lista de Confeitarias -->
          <div>
            <ConfectioneryList
              :confectioneries="confectioneries"
              :products="products"
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

    <!-- Modal de Confeitaria -->
    <Teleport to="body">
      <div v-if="showConfectioneryForm" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-screen items-center justify-center p-4">
          <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="closeConfectioneryForm"></div>
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
          <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="closeProductForm"></div>
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
import { ref, onMounted } from 'vue';
import ConfectioneryMap from '@/Components/map/ConfectioneryMap.vue';
import ConfectioneryList from '@/Components/confectionery/ConfectioneryList.vue';
import ConfectioneryForm from '@/Components/confectionery/ConfectioneryForm.vue';
import ProductForm from '@/Components/product/ProductForm.vue';
import { ConfectioneryAPI } from '@/services/api';

interface Props {
  initialConfectioneries: any[];
  initialProducts: any[];
}

const props = withDefaults(defineProps<Props>(), {
  initialConfectioneries: () => [],
  initialProducts: () => []
});

const confectioneries = ref(props.initialConfectioneries || []);
const products = ref(props.initialProducts || []);
const showConfectioneryForm = ref(false);
const showProductForm = ref(false);
const selectedConfectionery = ref(null);
const selectedProduct = ref(null);

const fetchData = async () => {
  try {
    const response = await ConfectioneryAPI.getAll();
    confectioneries.value = response.data.confectioneries;
    products.value = response.data.products;
  } catch (error) {
    console.error('Erro ao carregar dados:', error);
    alert('Erro ao carregar dados. Por favor, recarregue a página.');
  }
};

const editConfectionery = (confectionery) => {
  selectedConfectionery.value = confectionery;
  showConfectioneryForm.value = true;
};

const addProduct = (confectionery) => {
  selectedProduct.value = {
    confectionery_id: confectionery.id,
    name: '',
    price: 0,
    description: '',
    images: []
  };
  showProductForm.value = true;
};

const editProduct = (product) => {
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