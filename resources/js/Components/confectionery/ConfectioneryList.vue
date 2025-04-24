<template>
  <div class="space-y-8">
    <div v-for="confectionery in confectioneries" :key="confectionery.id" class="bg-white rounded-lg shadow-lg overflow-hidden">
      <!-- Cabeçalho da Confeitaria -->
      <div class="p-6 border-b border-gray-200">
        <div class="flex justify-between items-start">
          <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ confectionery.name }}</h2>
            <p class="mt-2 text-sm text-gray-600">
              {{ confectionery.address.street }}, {{ confectionery.address.number }}
              - {{ confectionery.address.neighborhood }}
            </p>
            <p class="text-sm text-gray-600">
              {{ confectionery.address.city }}/{{ confectionery.address.state }}
            </p>
            <p class="mt-2 text-sm text-gray-600">{{ confectionery.phone }}</p>
          </div>
          
          <div class="flex space-x-2">
            <button
              v-if="canEdit"
              @click="$emit('edit-confectionery', confectionery)"
              class="px-4 py-2 text-sm font-medium text-primary-600 hover:text-primary-800"
            >
              Editar
            </button>
            <button
              v-if="canDelete"
              @click="confirmDelete(confectionery)"
              class="px-4 py-2 text-sm font-medium text-red-600 hover:text-red-800"
            >
              Excluir
            </button>
          </div>
        </div>
      </div>

      <!-- Lista de Produtos -->
      <div class="p-6">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold text-gray-900">Produtos</h3>
          <button
            v-if="canAddProducts"
            @click="$emit('add-product', confectionery)"
            class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700"
          >
            Adicionar Produto
          </button>
        </div>

        <div v-if="getConfectioneryProducts(confectionery.id).length === 0" class="text-center py-8">
          <p class="text-gray-500">Nenhum produto cadastrado</p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <ProductCard
            v-for="product in getConfectioneryProducts(confectionery.id)"
            :key="product.id"
            :product="product"
            :can-edit="canEdit"
            :can-delete="canDelete"
            @edit="$emit('edit-product', product)"
            @delete="confirmDeleteProduct(product)"
          />
        </div>
      </div>
    </div>

    <!-- Modal de Confirmação -->
    <ConfirmationModal
      v-if="showDeleteModal"
      :title="deleteModalTitle"
      :message="deleteModalMessage"
      @confirm="handleDelete"
      @cancel="cancelDelete"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import ProductCard from '../product/ProductCard.vue';
import ConfirmationModal from '../shared/ConfirmationModal.vue';
import { ConfectioneryAPI, ProductAPI } from '@/services/api';

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
}

interface Product {
  id: number;
  confectionery_id: number;
  name: string;
  price: number;
  description: string;
  images: { id: number; url: string }[];
}

const props = withDefaults(defineProps<{
  confectioneries: Confectionery[];
  products: Product[];
  canEdit?: boolean;
  canDelete?: boolean;
  canAddProducts?: boolean;
}>(), {
  confectioneries: () => [],
  products: () => [],
  canEdit: false,
  canDelete: false,
  canAddProducts: false
});

const emit = defineEmits<{
  (e: 'edit-confectionery', confectionery: Confectionery): void;
  (e: 'edit-product', product: Product): void;
  (e: 'add-product', confectionery: Confectionery): void;
  (e: 'delete-confectionery', confectionery: Confectionery): void;
  (e: 'delete-product', product: Product): void;
  (e: 'refresh'): void;
}>();

const showDeleteModal = ref(false);
const deleteModalTitle = ref('');
const deleteModalMessage = ref('');
const itemToDelete = ref<Confectionery | Product | null>(null);
const deleteType = ref<'confectionery' | 'product'>('confectionery');

const getConfectioneryProducts = (confectioneryId: number) => {
  return props.products.filter(product => product.confectionery_id === confectioneryId);
};

const confirmDelete = (confectionery: Confectionery) => {
  deleteType.value = 'confectionery';
  itemToDelete.value = confectionery;
  deleteModalTitle.value = 'Excluir Confeitaria';
  deleteModalMessage.value = `Tem certeza que deseja excluir a confeitaria "${confectionery.name}"? 
    Todos os produtos vinculados serão excluídos permanentemente.`;
  showDeleteModal.value = true;
};

const confirmDeleteProduct = (product: Product) => {
  deleteType.value = 'product';
  itemToDelete.value = product;
  deleteModalTitle.value = 'Excluir Produto';
  deleteModalMessage.value = `Tem certeza que deseja excluir o produto "${product.name}"?`;
  showDeleteModal.value = true;
};

const handleDelete = async () => {
  try {
    if (deleteType.value === 'confectionery' && itemToDelete.value) {
      await ConfectioneryAPI.delete((itemToDelete.value as Confectionery).id);
      emit('delete-confectionery', itemToDelete.value as Confectionery);
    } else if (deleteType.value === 'product' && itemToDelete.value) {
      await ProductAPI.delete((itemToDelete.value as Product).id);
      emit('delete-product', itemToDelete.value as Product);
    }
    emit('refresh');
  } catch (error) {
    console.error('Erro ao excluir:', error);
    alert('Erro ao excluir. Por favor, tente novamente.');
  } finally {
    cancelDelete();
  }
};

const cancelDelete = () => {
  showDeleteModal.value = false;
  deleteModalTitle.value = '';
  deleteModalMessage.value = '';
  itemToDelete.value = null;
};
</script>
