<template>
  <div 
    class="card hover:shadow-lg transition-shadow relative overflow-hidden rounded-lg bg-white"
    :class="{'opacity-75': isLoadingAction}"
  >
    <!-- Carrossel de Imagens -->
    <div class="relative h-48 overflow-hidden">
      <div v-if="product.images && product.images.length > 0" class="h-full">
        <div class="relative h-full">
          <!-- Imagem principal do produto -->
          <img 
            :src="product.images[currentImageIndex].url"
            :alt="product.name"
            class="w-full h-full object-cover transition-opacity duration-300"
            @error="handleImageError"
          />
          
          <!-- Navegação do carrossel (somente se houver múltiplas imagens) -->
          <div v-if="product.images.length > 1" class="absolute inset-0 flex items-center justify-between p-2">
            <!-- Botão para imagem anterior -->
            <button 
              @click.stop="prevImage" 
              class="p-1 rounded-full bg-primary-800 bg-opacity-50 text-white hover:bg-opacity-75 focus:outline-none"
              aria-label="Imagem anterior"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
              </svg>
            </button>
            
            <!-- Botão para próxima imagem -->
            <button 
              @click.stop="nextImage" 
              class="p-1 rounded-full bg-primary-800 bg-opacity-50 text-white hover:bg-opacity-75 focus:outline-none"
              aria-label="Próxima imagem"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </button>
          </div>
          
          <!-- Indicadores de posição no carrossel -->
          <div v-if="product.images.length > 1" class="absolute bottom-2 left-0 right-0 flex justify-center space-x-2">
            <button
              v-for="(_, index) in product.images"
              :key="index"
              @click.stop="currentImageIndex = index"
              class="w-2 h-2 rounded-full focus:outline-none transition-colors duration-200"
              :class="index === currentImageIndex ? 'bg-white' : 'bg-white bg-opacity-50'"
              :aria-label="`Ver imagem ${index + 1}`"
            ></button>
          </div>
        </div>
      </div>
      <!-- Placeholder para produtos sem imagens -->
      <div v-else class="w-full h-full flex items-center justify-center bg-primary-100 text-primary-400">
        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
      </div>
    </div>

    <!-- Informações do produto -->
    <div class="card-body p-4">
      <h3 class="text-base sm:text-lg font-semibold text-primary-900 mb-1 sm:mb-2 line-clamp-2">{{ product.name }}</h3>
      <p class="text-primary-600 text-xs sm:text-sm mb-2 line-clamp-2">{{ product.description }}</p>
      <div class="flex flex-wrap justify-between items-center mt-2 sm:mt-4">
        <div class="text-primary-700 font-medium">{{ formattedPrice }}</div>
      </div>
    </div>
    
    <!-- Botões de ação com posição fixa -->
    <div v-if="canEdit || canDelete" class="absolute top-2 right-2 flex space-x-1.5 z-10">
      <!-- Botão de edição -->
      <button
        v-if="canEdit"
        @click.stop="handleEdit"
        class="bg-white rounded-full shadow-md p-1.5 text-primary-600 hover:text-primary-800 hover:bg-primary-50 transition-colors"
        :disabled="isLoadingAction"
        title="Editar produto"
      >
        <span class="sr-only">Editar</span>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
          <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8-379-2.83-2.828z" />
        </svg>
      </button>
      
      <!-- Botão de exclusão -->
      <button
        v-if="canDelete"
        @click.stop="handleDelete"
        class="bg-white rounded-full shadow-md p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 transition-colors"
        :disabled="isLoadingAction"
        title="Excluir produto"
      >
        <span class="sr-only">Excluir</span>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
        </svg>
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
/**
 * Componente ProductCard
 * 
 * Exibe um cartão de produto com imagens em carrossel, detalhes
 * e botões de ação (editar/excluir) quando necessário.
 */
import { ref, computed } from 'vue';
import type { Product } from '@/types/';

// Props do componente
const props = defineProps<{
  product: Product;
  canEdit?: boolean;
  canDelete?: boolean;
}>();

// Eventos emitidos pelo componente
const emit = defineEmits<{
  (e: 'edit', product: Product): void;
  (e: 'delete', product: Product): void;
}>();

// Estado do controle de imagens
const currentImageIndex = ref(0);
const isLoadingAction = ref(false);

/**
 * Avança para a próxima imagem no carrossel
 */
const nextImage = (event?: Event) => {
  if (event) {
    event.stopPropagation();
  }
  
  if (!props.product.images?.length) return;
  currentImageIndex.value = (currentImageIndex.value + 1) % props.product.images.length;
};

/**
 * Retorna para a imagem anterior no carrossel
 */
const prevImage = (event?: Event) => {
  if (event) {
    event.stopPropagation();
  }
  
  if (!props.product.images?.length) return;
  currentImageIndex.value = currentImageIndex.value === 0 
    ? props.product.images.length - 1 
    : currentImageIndex.value - 1;
};

/**
 * Trata erros de carregamento de imagem
 * Exibe uma imagem de placeholder se a imagem original falhar
 */
const handleImageError = (event: Event) => {
  const target = event.target as HTMLImageElement;
  target.src = '/storage/placeholder-image.jpg'; // Substitua pelo caminho da sua imagem de placeholder
};

/**
 * Formata o preço do produto para o formato brasileiro (R$)
 */
const formattedPrice = computed(() => {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  }).format(props.product.price);
});

/**
 * Manipula o evento de edição do produto
 */
const handleEdit = () => {
  isLoadingAction.value = true;
  try {
    emit('edit', props.product);
  } finally {
    // Importante reiniciar o estado mesmo em caso de erro
    setTimeout(() => {
      isLoadingAction.value = false;
    }, 300);
  }
};

/**
 * Manipula o evento de exclusão do produto
 */
const handleDelete = () => {
  isLoadingAction.value = true;
  try {
    emit('delete', props.product);
  } finally {
    // Importante reiniciar o estado mesmo em caso de erro
    setTimeout(() => {
      isLoadingAction.value = false;
    }, 300);
  }
};
</script>

<style scoped>
/* Efeito de transição suave entre imagens */
.card img {
  transition: opacity 0.3s ease-in-out;
}

/* Impede múltiplos cliques em ações */
.card button:disabled {
  cursor: not-allowed;
  opacity: 0.7;
}

/* Limitador de linhas para textos longos */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>